<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ReturnController extends Controller
{
    /**
     * Column/table names differ between customer and staff returns; every
     * public method resolves them once here rather than branching inline.
     */
    private function resolveEntities(string $billingType): array
    {
        if ($billingType === 'staff') {
            return [
                'billing_table'     => 'staff_billing',
                'item_table'        => 'staff_product_billing',
                'phone_col'         => 'staff_phone',
                'name_col'          => 'staff_name',
                'credit_table'      => 'credit_note_staff',
                'credit_item_table' => 'credit_note_staff_items',
                'prefix'            => 'S',
            ];
        }

        return [
            'billing_table'     => 'customer_billing',
            'item_table'        => 'customer_product_billing',
            'phone_col'         => 'customer_phone',
            'name_col'          => 'customer_name',
            'credit_table'      => 'credit_note_customer',
            'credit_item_table' => 'credit_note_customer_items',
            'prefix'            => 'C',
        ];
    }

    private function resolveStoreId(): ?int
    {
        $storeMetaId = session('storeId');
        if (!$storeMetaId) {
            return null;
        }

        $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
        return $store?->id;
    }

    private function generateCreditNoteNumber(int $storeId, string $billingType, string $creditTable): string
    {
        $yy = date('y');
        $mm = date('m');
        $prefix = $billingType === 'staff' ? 'S' : 'C';
        $storeCode = $prefix . str_pad($storeId, 3, '0', STR_PAD_LEFT);

        $count = DB::table($creditTable)
            ->where('store_id', $storeId)
            ->whereYear('return_date', date('Y'))
            ->whereMonth('return_date', date('m'))
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return '#CRN' . $storeCode . $yy . $mm . $sequence;
    }

    public function getEligibleBills(Request $request)
    {
        try {
            $storeId = $this->resolveStoreId();
            if (!$storeId) {
                return response()->json(['status' => 403, 'message' => 'Store not logged in.'], 403);
            }

            $billingType = $request->query('billing_type', 'customer');
            if (!in_array($billingType, ['customer', 'staff'], true)) {
                return response()->json(['status' => 400, 'message' => 'Invalid billing type.'], 400);
            }

            $phone = $request->query('phone');
            if (!$phone) {
                return response()->json(['status' => 400, 'message' => 'Phone number is required.'], 400);
            }

            $e = $this->resolveEntities($billingType);
            $cutoff = now()->subMonth()->format('Y-m-d');

            $bills = DB::table($e['billing_table'])
                ->where($e['phone_col'], $phone)
                ->where('store_id', $storeId)
                ->where('billing_date', '>=', $cutoff)
                ->whereNull('edited_at') // an edited bill can no longer be returned from — see editSameDayBilling
                ->orderByDesc('billing_date')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            $data = $bills->map(function ($bill) use ($e) {
                $items = DB::table($e['item_table'])
                    ->leftJoin('product', $e['item_table'] . '.productId', '=', 'product.id')
                    ->leftJoin('inhouse_product', $e['item_table'] . '.inhouse_product_id', '=', 'inhouse_product.id')
                    ->leftJoin('brand', 'product.brand_id', '=', 'brand.id')
                    ->where($e['item_table'] . '.cb_id', $bill->id)
                    ->select(
                        $e['item_table'] . '.*',
                        DB::raw('COALESCE(product.product_name, inhouse_product.product_name) as product_name'),
                        DB::raw('COALESCE(brand.brand_name, "Inhouse") as brand_name')
                    )
                    ->get()
                    ->map(function ($item) use ($e) {
                        // Inhouse products have no purchase_request batch to restore stock
                        // into, so they're never eligible for return.
                        if (!empty($item->inhouse_product_id)) {
                            $item->returnable_qty = 0;
                            return $item;
                        }
                        $alreadyReturned = DB::table($e['credit_item_table'])
                            ->where('source_item_id', $item->id)
                            ->sum('qty');
                        $item->returnable_qty = (float) $item->qty - (float) $alreadyReturned;
                        return $item;
                    });

                return ['bill' => $bill, 'items' => $items];
            });

            return response()->json(['status' => 200, 'data' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function createReturn(Request $request)
    {
        try {
            $storeId = $this->resolveStoreId();
            if (!$storeId) {
                return response()->json(['status' => 403, 'message' => 'Store not logged in.'], 403);
            }

            $billingType = $request->billing_type;
            if (!in_array($billingType, ['customer', 'staff'], true)) {
                return response()->json(['status' => 400, 'message' => 'Invalid billing type.'], 400);
            }

            $sourceBillId = $request->source_bill_id;
            $phone = $request->phone;
            $name = $request->name;
            $returnItems = $request->return_items ?? [];

            if (!$sourceBillId || !$phone || empty($returnItems)) {
                return response()->json(['status' => 400, 'message' => 'Missing bill, phone, or return items.'], 400);
            }

            $e = $this->resolveEntities($billingType);

            $bill = DB::table($e['billing_table'])->where('id', $sourceBillId)->where('store_id', $storeId)->first();
            if (!$bill) {
                return response()->json(['status' => 404, 'message' => 'Source bill not found.'], 404);
            }
            if ($bill->{$e['phone_col']} !== $phone) {
                return response()->json(['status' => 400, 'message' => 'Phone number does not match the source bill.'], 400);
            }
            $cutoff = now()->subMonth()->format('Y-m-d');
            if ($bill->billing_date < $cutoff) {
                return response()->json(['status' => 400, 'message' => 'This bill is outside the 1-month return window.'], 400);
            }
            if (!empty($bill->edited_at)) {
                return response()->json(['status' => 400, 'message' => 'This bill has been edited and cannot be returned.'], 400);
            }

            DB::beginTransaction();

            $totalCredit = 0;
            $itemsToInsert = [];

            foreach ($returnItems as $ri) {
                $returnQty = (float) ($ri['qty'] ?? 0);
                if ($returnQty <= 0) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'Invalid return quantity.'], 400);
                }

                $original = DB::table($e['item_table'])
                    ->where('id', $ri['source_item_id'] ?? null)
                    ->where('cb_id', $sourceBillId)
                    ->first();
                if (!$original) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'One of the return items was not found on this bill.'], 400);
                }

                if (!empty($original->inhouse_product_id)) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => "Inhouse products ({$original->pack}) are not returnable."], 400);
                }

                $alreadyReturned = DB::table($e['credit_item_table'])->where('source_item_id', $original->id)->sum('qty');
                $maxReturnable = (float) $original->qty - (float) $alreadyReturned;
                if ($returnQty > $maxReturnable) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => "Return quantity exceeds the remaining returnable quantity for {$original->pack}."], 400);
                }

                $pack = DB::table('pack')->where('pack_name', $original->pack)->first();
                if ($pack) {
                    // A product+pack can have several purchase_request batch rows at
                    // different prices (and several more at the SAME price from different
                    // expiry batches). Narrow by price first — restoring qty to a
                    // different-priced batch would silently misprice future stock, which
                    // is worse than the remaining (accepted) same-price-batch ambiguity.
                    $prQuery = DB::table('purchase_request')
                        ->where('product_id', $original->productId)
                        ->where('pack_id', $pack->id);

                    $priceRow = DB::table('price')->where('price_name', $original->unitValue)->first();
                    if ($priceRow) {
                        $prQuery->where('price_id', $priceRow->id);
                    }

                    $pr = $prQuery->orderBy('id')->first();
                    if ($pr) {
                        DB::table('purchase_request')->where('id', $pr->id)->increment('qty', $returnQty);
                    }
                }

                $ratio = $returnQty / (float) $original->qty;
                $lineCredit = round((float) $original->totalAmount * $ratio, 2);
                $lineGst = $original->gstAmount !== null ? round((float) $original->gstAmount * $ratio, 2) : null;
                $lineGstRate = $original->gstRate;

                $totalCredit += $lineCredit;

                $itemsToInsert[] = [
                    'source_item_id'      => $original->id,
                    'productId'           => $original->productId,
                    'inhouse_product_id'  => $original->inhouse_product_id,
                    'pack'                => $original->pack,
                    'qty'                 => $returnQty,
                    'unitValue'           => $original->unitValue,
                    'totalAmount'         => $lineCredit,
                    'gstRate'             => $lineGstRate,
                    'gstAmount'           => $lineGst,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }

            $creditNoteNo = $this->generateCreditNoteNumber($storeId, $billingType, $e['credit_table']);

            $insertId = DB::table($e['credit_table'])->insertGetId([
                'credit_note_no'    => $creditNoteNo,
                'store_id'          => $storeId,
                $e['phone_col']     => $phone,
                $e['name_col']      => $name,
                'source_bill_id'    => $sourceBillId,
                'source_invoice_no' => $bill->invoiceNo,
                'total_credit_amt'  => $totalCredit,
                'status'            => 'active',
                'return_date'       => now()->format('Y-m-d'),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            foreach ($itemsToInsert as $row) {
                $row['credit_note_id'] = $insertId;
                DB::table($e['credit_item_table'])->insert($row);
            }

            DB::commit();

            return response()->json([
                'status'           => 200,
                'credit_note_id'   => $insertId,
                'credit_note_no'   => $creditNoteNo,
                'total_credit_amt' => $totalCredit,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function listCreditNotes(Request $request)
    {
        try {
            $storeId = $this->resolveStoreId();
            if (!$storeId) {
                return response()->json(['status' => 403, 'message' => 'Store not logged in.'], 403);
            }

            $billingType = $request->query('billing_type', 'customer');
            if (!in_array($billingType, ['customer', 'staff'], true)) {
                return response()->json(['status' => 400, 'message' => 'Invalid billing type.'], 400);
            }
            $e = $this->resolveEntities($billingType);

            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $search = $request->query('search');
            $status = $request->query('status');
            $page = $request->query('page');
            $limit = $request->query('limit');

            $query = DB::table($e['credit_table'])->where('store_id', $storeId);

            if ($startDate) {
                $query->where('return_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('return_date', '<=', $endDate);
            }
            if ($search) {
                $query->where('credit_note_no', 'like', '%' . $search . '%');
            }
            if ($status) {
                $query->where('status', $status);
            }

            $query->orderByDesc('id');

            if ($page && $limit) {
                $creditNotes = $query->paginate($limit, ['*'], 'page', $page);
            } else {
                $creditNotes = $query->get();
            }

            return response()->json(['status' => 200, 'data' => $creditNotes], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function getCreditNoteDetails($id, Request $request)
    {
        try {
            $billingType = $request->query('billing_type', 'customer');
            if (!in_array($billingType, ['customer', 'staff'], true)) {
                return response()->json(['status' => 400, 'message' => 'Invalid billing type.'], 400);
            }
            $e = $this->resolveEntities($billingType);

            $creditNote = DB::table($e['credit_table'])->where('id', $id)->first();
            if (!$creditNote) {
                return response()->json(['status' => 404, 'message' => 'Credit note not found.'], 404);
            }

            $items = DB::table($e['credit_item_table'])
                ->leftJoin('product', $e['credit_item_table'] . '.productId', '=', 'product.id')
                ->leftJoin('inhouse_product', $e['credit_item_table'] . '.inhouse_product_id', '=', 'inhouse_product.id')
                ->leftJoin('brand', 'product.brand_id', '=', 'brand.id')
                ->where($e['credit_item_table'] . '.credit_note_id', $id)
                ->select(
                    $e['credit_item_table'] . '.*',
                    DB::raw('COALESCE(product.product_name, inhouse_product.product_name) as product_name'),
                    DB::raw('COALESCE(brand.brand_name, "Inhouse") as brand_name')
                )
                ->get();

            $store = DB::table('store')->where('id', $creditNote->store_id)->first();

            return response()->json([
                'status' => 200,
                'data'   => [
                    'credit_note' => $creditNote,
                    'items'       => $items,
                    'store'       => $store,
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function getActiveCreditNoteForPhone(Request $request)
    {
        try {
            $phone = $request->query('phone');
            if (!$phone) {
                return response()->json(['status' => 400, 'message' => 'Phone is required.'], 400);
            }

            $customerNotes = DB::table('credit_note_customer')
                ->where('customer_phone', $phone)
                ->where('status', 'active')
                ->select('credit_note_no', 'total_credit_amt', 'return_date')
                ->get();

            $staffNotes = DB::table('credit_note_staff')
                ->where('staff_phone', $phone)
                ->where('status', 'active')
                ->select('credit_note_no', 'total_credit_amt', 'return_date')
                ->get();

            return response()->json(['status' => 200, 'data' => $customerNotes->merge($staffNotes)->values()], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function validateCreditNote(Request $request)
    {
        try {
            $creditNoteNo = $request->query('credit_note_no');
            $phone = $request->query('phone');

            if (!$creditNoteNo || !$phone) {
                return response()->json(['status' => 400, 'message' => 'Credit note number and phone are required.'], 400);
            }

            $creditNote = DB::table('credit_note_customer')->where('credit_note_no', $creditNoteNo)->first();
            $phoneField = 'customer_phone';

            if (!$creditNote) {
                $creditNote = DB::table('credit_note_staff')->where('credit_note_no', $creditNoteNo)->first();
                $phoneField = 'staff_phone';
            }

            if (!$creditNote) {
                return response()->json(['status' => 404, 'message' => 'Credit note not found.'], 404);
            }

            if ($creditNote->status !== 'active') {
                return response()->json(['status' => 400, 'message' => 'This credit note has already been redeemed.'], 400);
            }

            if ($creditNote->{$phoneField} !== $phone) {
                return response()->json(['status' => 400, 'message' => 'This credit note does not belong to this phone number.'], 400);
            }

            return response()->json([
                'status'           => 200,
                'credit_note_no'   => $creditNote->credit_note_no,
                'total_credit_amt' => $creditNote->total_credit_amt,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }
}
