<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class StockTransferController extends Controller
{
    public function createTransfer(Request $request)
    {
        try {
            $storeMetaId = session('storeId');
            if (!$storeMetaId) {
                return response()->json(['status' => 403, 'message' => 'Store not logged in.'], 403);
            }

            $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
            if (!$store) {
                return response()->json(['status' => 404, 'message' => 'Store not found.'], 404);
            }

            $fromStoreId = $store->id;
            $toStoreId   = $request->to_store_id;
            $items       = $request->items ?? [];
            $notes       = $request->notes;

            if (!$toStoreId) {
                return response()->json(['status' => 400, 'message' => 'Please select a destination store.'], 400);
            }

            if (empty($items)) {
                return response()->json(['status' => 400, 'message' => 'No items to transfer.'], 400);
            }

            $transferNo = $this->generateTransferNumber($fromStoreId);

            DB::beginTransaction();

            foreach ($items as $item) {
                $pr = DB::table('purchase_request')->where('id', $item['purchase_request_id'])->first();

                if (!$pr) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => "Product batch not found for {$item['product_name']}."], 400);
                }

                if ((float)$pr->qty < (float)$item['qty']) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => "Insufficient stock for {$item['product_name']}. Available: {$pr->qty}."], 400);
                }

                DB::table('purchase_request')->where('id', $pr->id)->update([
                    'qty'        => (string)((float)$pr->qty - (float)$item['qty']),
                    'updated_at' => now(),
                ]);
            }

            $transferId = DB::table('stock_transfer')->insertGetId([
                'transfer_no'   => $transferNo,
                'from_store_id' => $fromStoreId,
                'to_store_id'   => $toStoreId,
                'transfer_date' => date('Y-m-d'),
                'status'        => 'pending',
                'notes'         => $notes,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            foreach ($items as $item) {
                DB::table('stock_transfer_items')->insert([
                    'transfer_id'         => $transferId,
                    'purchase_request_id' => $item['purchase_request_id'],
                    'product_id'          => $item['product_id'],
                    'product_name'        => $item['product_name'],
                    'pack_id'             => $item['pack_id'],
                    'pack_name'           => $item['pack_name'],
                    'price_id'            => $item['price_id'],
                    'brand_id'            => $item['brand_id'] ?? null,
                    'unit_value'          => $item['unit_value'],
                    'qty'                 => $item['qty'],
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'status'      => 200,
                'message'     => 'Stock transfer created successfully.',
                'transfer_no' => $transferNo,
                'transfer_id' => $transferId,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    private function generateTransferNumber(int $fromStoreId): string
    {
        $yy        = date('y');
        $mm        = date('m');
        $storeCode = 'S' . str_pad($fromStoreId, 3, '0', STR_PAD_LEFT);

        $count = DB::table('stock_transfer')
            ->where('from_store_id', $fromStoreId)
            ->whereYear('transfer_date', date('Y'))
            ->whereMonth('transfer_date', date('m'))
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return '#TRF' . $storeCode . $yy . $mm . $sequence;
    }

    public function listTransfers(Request $request)
    {
        try {
            $storeMetaId = session('storeId');
            $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();

            if (!$store) {
                return response()->json(['status' => 404, 'message' => 'Store not found.'], 404);
            }

            $storeId   = $store->id;
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $type      = $request->query('type', 'sent'); // sent | received

            $query = DB::table('stock_transfer');

            if ($type === 'sent') {
                $query->where('from_store_id', $storeId);
            } else {
                $query->where('to_store_id', $storeId);
            }

            if ($startDate) $query->where('transfer_date', '>=', $startDate);
            if ($endDate)   $query->where('transfer_date', '<=', $endDate);

            $transfers = $query->orderByDesc('transfer_date')->orderByDesc('id')->get();

            $storeCache = [];
            $transfers = $transfers->map(function ($t) use (&$storeCache) {
                $itemCount = DB::table('stock_transfer_items')->where('transfer_id', $t->id)->count();

                foreach ([$t->from_store_id, $t->to_store_id] as $sid) {
                    if (!isset($storeCache[$sid])) {
                        $s = DB::table('store')->where('id', $sid)->first();
                        if (!$s) {
                            $s = DB::connection('remote_mysql')->table('store')->where('id', $sid)->first();
                        }
                        $storeCache[$sid] = $s?->name ?? "Store #{$sid}";
                    }
                }

                return [
                    'id'              => $t->id,
                    'transfer_no'     => $t->transfer_no,
                    'from_store_id'   => $t->from_store_id,
                    'from_store_name' => $storeCache[$t->from_store_id],
                    'to_store_id'     => $t->to_store_id,
                    'to_store_name'   => $storeCache[$t->to_store_id],
                    'transfer_date'   => $t->transfer_date,
                    'status'          => $t->status,
                    'received_at'     => $t->received_at,
                    'notes'           => $t->notes,
                    'item_count'      => $itemCount,
                ];
            });

            return response()->json(['status' => 200, 'data' => $transfers], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function getTransferDetail($id)
    {
        try {
            $transfer = DB::table('stock_transfer')->where('id', $id)->first();

            if (!$transfer) {
                return response()->json(['status' => 404, 'message' => 'Transfer not found.'], 404);
            }

            $items     = DB::table('stock_transfer_items')->where('transfer_id', $id)->get();
            $fromStore = DB::table('store')->where('id', $transfer->from_store_id)->first()
                ?? DB::connection('remote_mysql')->table('store')->where('id', $transfer->from_store_id)->first();
            $toStore   = DB::table('store')->where('id', $transfer->to_store_id)->first()
                ?? DB::connection('remote_mysql')->table('store')->where('id', $transfer->to_store_id)->first();

            return response()->json([
                'status' => 200,
                'data'   => [
                    'transfer'        => $transfer,
                    'items'           => $items,
                    'from_store_name' => $fromStore?->name ?? "Store #{$transfer->from_store_id}",
                    'to_store_name'   => $toStore?->name ?? "Store #{$transfer->to_store_id}",
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }
}
