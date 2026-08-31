<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class StockWriteoffController extends Controller
{
    public function createWriteoff(Request $request)
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

            $storeId = $store->id;
            $items   = $request->items ?? [];
            $notes   = $request->notes;

            if (empty($items)) {
                return response()->json(['status' => 400, 'message' => 'No items to write off.'], 400);
            }

            $writeoffNo = $this->generateWriteoffNumber($storeId);

            DB::beginTransaction();

            $itemsToInsert = [];
            foreach ($items as $item) {
                $deductions = $this->deductFifo(
                    (int) $item['product_id'],
                    (int) $item['pack_id'],
                    (int) $item['price_id'],
                    (float) $item['qty']
                );

                if ($deductions === null) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => "Insufficient stock for {$item['product_name']}."], 400);
                }

                foreach ($deductions as $d) {
                    $itemsToInsert[] = [
                        'purchase_request_id' => $d['purchase_request_id'],
                        'product_id'          => $item['product_id'],
                        'product_name'        => $item['product_name'],
                        'pack_id'             => $item['pack_id'],
                        'pack_name'           => $item['pack_name'],
                        'price_id'            => $item['price_id'],
                        'brand_id'            => $item['brand_id'] ?? null,
                        'unit_value'          => $item['unit_value'],
                        'qty'                 => (string) $d['qty'],
                        'reason'              => $item['reason'],
                    ];
                }
            }

            $writeoffId = DB::table('stock_writeoff')->insertGetId([
                'writeoff_no'   => $writeoffNo,
                'store_id'      => $storeId,
                'writeoff_date' => date('Y-m-d'),
                'notes'         => $notes,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            foreach ($itemsToInsert as $row) {
                DB::table('stock_writeoff_items')->insert(array_merge($row, [
                    'writeoff_id' => $writeoffId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]));
            }

            DB::commit();

            return response()->json([
                'status'      => 200,
                'message'     => 'Stock write-off recorded successfully.',
                'writeoff_no' => $writeoffNo,
                'writeoff_id' => $writeoffId,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    // Batches are merged in the picker UI (no batch/expiry number shown to staff), so
    // the qty entered may span more than one purchase_request row at the same price.
    // Deduct oldest-expiry-first across those batches, splitting into one deduction
    // per batch actually touched. Returns null if total available stock is short.
    private function deductFifo(int $productId, int $packId, int $priceId, float $qty): ?array
    {
        $batches = DB::table('purchase_request')
            ->where('product_id', $productId)
            ->where('pack_id', $packId)
            ->where('price_id', $priceId)
            ->where('qty', '>', 0)
            ->where(function ($q) {
                $q->whereNull('exp_date')->orWhere('exp_date', '>', now()->toDateString());
            })
            ->orderByRaw('exp_date IS NULL')
            ->orderBy('exp_date')
            ->orderBy('id')
            ->get();

        if ($batches->sum(fn ($b) => (float) $b->qty) < $qty) {
            return null;
        }

        $remaining   = $qty;
        $deductions  = [];
        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $take = min((float) $batch->qty, $remaining);
            if ($take <= 0) continue;

            DB::table('purchase_request')->where('id', $batch->id)->update([
                'qty'        => (string) ((float) $batch->qty - $take),
                'updated_at' => now(),
            ]);

            $deductions[] = ['purchase_request_id' => $batch->id, 'qty' => $take];
            $remaining -= $take;
        }

        return $deductions;
    }

    private function generateWriteoffNumber(int $storeId): string
    {
        $yy        = date('y');
        $mm        = date('m');
        $storeCode = 'S' . str_pad($storeId, 3, '0', STR_PAD_LEFT);

        $count = DB::table('stock_writeoff')
            ->where('store_id', $storeId)
            ->whereYear('writeoff_date', date('Y'))
            ->whereMonth('writeoff_date', date('m'))
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return '#WOF' . $storeCode . $yy . $mm . $sequence;
    }

    public function listWriteoffs(Request $request)
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

            $query = DB::table('stock_writeoff')->where('store_id', $storeId);

            if ($startDate) $query->where('writeoff_date', '>=', $startDate);
            if ($endDate)   $query->where('writeoff_date', '<=', $endDate);

            $writeoffs = $query->orderByDesc('writeoff_date')->orderByDesc('id')->get();

            $writeoffs = $writeoffs->map(function ($w) {
                $itemCount = DB::table('stock_writeoff_items')->where('writeoff_id', $w->id)->count();
                $totalQty  = DB::table('stock_writeoff_items')->where('writeoff_id', $w->id)->sum('qty');

                return [
                    'id'            => $w->id,
                    'writeoff_no'   => $w->writeoff_no,
                    'writeoff_date' => $w->writeoff_date,
                    'notes'         => $w->notes,
                    'item_count'    => $itemCount,
                    'total_qty'     => $totalQty,
                ];
            });

            return response()->json(['status' => 200, 'data' => $writeoffs], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function getWriteoffDetail($id)
    {
        try {
            $writeoff = DB::table('stock_writeoff')->where('id', $id)->first();

            if (!$writeoff) {
                return response()->json(['status' => 404, 'message' => 'Write-off not found.'], 404);
            }

            $items = DB::table('stock_writeoff_items')->where('writeoff_id', $id)->get();

            return response()->json([
                'status' => 200,
                'data'   => [
                    'writeoff' => $writeoff,
                    'items'    => $items,
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }
}
