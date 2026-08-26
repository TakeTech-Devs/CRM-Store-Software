<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Customer;


class CustomerBilling extends Controller 
{
      
    public function createBilling(Request $request)
    {
        try {
            // Retrieve storeId from the session (set during login)
            $storeMetaId = session('storeId');

            if (!$storeMetaId) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Store not logged in.'
                ], 403);
            }

            $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
            $storeId = $store?->id;

            // Generate invoice number: #INVS{store_id_padded}{YY}{MM}{sequence}
            $invoiceNo = $this->generateInvoiceNumber($storeId);

            // Billing and product details from the request
            $customer_phone = $request->customer_phone;
            $doctor_name = $request->doctor_name;
            $paymentType = $request->paymentType;
            $product_billing = $request->product_billings;
            $billing_date = $request->billing_date;
            $customer_name = $request->customer_name;
            $billingType = $request->billingType;
            $total_amt = $request->total_amt;
            $gstAmount = $request->gstAmount;
            $cgst = $request->cgst;
            $sgst = $request->sgst;
            $creditNoteNo = $request->credit_note_no ?: null;

            DB::beginTransaction();

            // If a credit note is being redeemed on this bill, validate and lock it now
            // (lockForUpdate here, unlike the rest of this codebase, since a double-redeem
            // is a real money leak, not a cosmetic duplicate invoice number).
            $creditNoteRecord = null;
            $creditNoteTable = null;
            $creditAppliedAmt = null;
            if ($creditNoteNo) {
                $creditNoteRecord = DB::table('credit_note_customer')->where('credit_note_no', $creditNoteNo)->lockForUpdate()->first();
                $creditNoteTable = 'credit_note_customer';
                if (!$creditNoteRecord) {
                    $creditNoteRecord = DB::table('credit_note_staff')->where('credit_note_no', $creditNoteNo)->lockForUpdate()->first();
                    $creditNoteTable = 'credit_note_staff';
                }

                if (!$creditNoteRecord || $creditNoteRecord->status !== 'active') {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'Invalid or already-redeemed credit note.'], 400);
                }

                $creditNotePhone = $creditNoteRecord->customer_phone ?? $creditNoteRecord->staff_phone;
                if ($creditNotePhone !== $customer_phone) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'This credit note does not belong to this phone number.'], 400);
                }

                if ((float) $total_amt < (float) $creditNoteRecord->total_credit_amt) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'Bill total must be at least the credit note value.'], 400);
                }

                $creditAppliedAmt = $creditNoteRecord->total_credit_amt;
            }

            // Loop through product billing — deduct stock only for regular items
            foreach ($product_billing as $key => $value) {
                if (filter_var($value['is_inhouse'] ?? false, FILTER_VALIDATE_BOOLEAN)) continue;

                $prIds = $value['purchase_request_ids'] ?? [];
                if (empty($prIds)) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'No stock batches found for product.'], 400);
                }

                $remaining = (int) $value['qty'];
                foreach ($prIds as $prId) {
                    if ($remaining <= 0) break;
                    $pr = DB::table('purchase_request')->where('id', $prId)->first();
                    if (!$pr || $pr->qty <= 0) continue;
                    $deduct = min($pr->qty, $remaining);
                    DB::table('purchase_request')->where('id', $prId)->update([
                        'qty' => $pr->qty - $deduct,
                        'updated_at' => now(),
                    ]);
                    $remaining -= $deduct;
                }

                if ($remaining > 0) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'Insufficient stock for one or more products.'], 400);
                }
            }

            // Insert into customer_billing table including the store_id
            $insert_cb = DB::table('customer_billing')->insertGetId([
                'store_id' => $storeId, // Use storeId from session
                'customer_phone' => $customer_phone,
                'customer_name' => $customer_name,
                'doctor_name' => $doctor_name,
                'invoiceNo' => $invoiceNo,
                'paymentType' => $paymentType,
                'billing_date' => $billing_date,
                'billingType' => $billingType,
                'total_amt' => $total_amt,
                'credit_note_no' => $creditNoteNo,
                'credit_applied_amt' => $creditAppliedAmt,
                'created_at' => now(),
                'updated_at' => now(),
                'gst'=> $gstAmount,
                'cgst'=> $cgst,
                'sgst'=> $sgst,
            ]);

            if ($creditNoteRecord) {
                DB::table($creditNoteTable)->where('id', $creditNoteRecord->id)->update([
                    'status' => 'redeemed',
                    'redeemed_bill_type' => 'customer',
                    'redeemed_bill_id' => $insert_cb,
                    'redeemed_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert into customer_product_billing table
            foreach ($product_billing as $key => $value) {
                DB::table('customer_product_billing')->insert([
                    'category' => $value['category'],
                    'subCategory' => $value['subCategory'],
                    'discount' => $value['discount'],
                    'pack' => $value['pack'],
                    'productId' => $value['productId'] ?? null,
                    'inhouse_product_id' => $value['inhouse_product_id'] ?? null,
                    'qty' => $value['qty'],
                    'totalAmount' => $value['totalAmount'],
                    'unitValue' => $value['unitValue'],
                    'cb_id' => $insert_cb,
                    'gstRate' => $value['gstRate'],
                    'gstAmount' => $value['gstAmount'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' => 200,
                'data' => 'Success',
                'bill_id' => $insert_cb
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    private function generateInvoiceNumber(int $storeId): string
    {
        $yy  = date('y');   // e.g. 26
        $mm  = date('m');   // e.g. 06
        $storeCode = 'C' . str_pad($storeId, 3, '0', STR_PAD_LEFT);  // e.g. C003

        $count = DB::table('customer_billing')
            ->where('store_id', $storeId)
            ->whereYear('billing_date', date('Y'))
            ->whereMonth('billing_date', date('m'))
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);  // e.g. 001

        return '#INV' . $storeCode . $yy . $mm . $sequence;
    }

    public function listBilling(Request $request){
        
        try {

            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $search = $request->query('search');
            $page = $request->query('page') ;
            $limit = $request->query('limit');
            $billingType = $request->billingType;
            $query = DB::table('customer_billing')->select(
                'customer_billing.*',
                DB::raw('EXISTS(SELECT 1 FROM credit_note_customer WHERE credit_note_customer.source_bill_id = customer_billing.id) as has_return')
            );

            if ($billingType) {
                $query->where('billingType', '=', $billingType);
            }
            
            if ($startDate) {
                $query->where('billing_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('billing_date', '<=', $endDate);
            }
            if ($search) {
                $query->where('invoiceNo', 'like', '%' . $search . '%');
            }
            if ($page && $limit ) { 
                $billing_details = $query->paginate($limit, ['*'], 'page', $page ?? 1);
                
            }else{

                $billing_details = $query->get();
            }

            if ($billing_details->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $billing_details
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'data' => 'No records found'
                ], 404);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create_customer(Request $request){
        try {
            // Single-store local DB — always exactly one row in store table
            $store = DB::table('store')->first();
            $storeKey = $store->id ?? 0;

            // Generate globally unique cus_id: CUS-{storeKey}-{00001}
            $last = DB::table('customer')
                ->where('cus_id', 'like', "CUS-{$storeKey}-%")
                ->orderByDesc('id')
                ->value('cus_id');

            $next  = $last ? ((int) explode('-', $last)[2]) + 1 : 1;
            $cusId = 'CUS-' . $storeKey . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);

            $payload = [
                'cus_id'     => $cusId,
                'store_id'   => $storeKey,
                'name'       => $request->name,
                'mail'       => $request->mail,
                'phone'      => $request->phone,
                'status'     => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $customer = Customer::insert($payload);
            if ($customer) {
                return response()->json([
                    'status' => 200,
                    'data'   => $cusId,
                ], 200);
            }

            return response()->json(['status' => 404, 'data' => 'Failed to create customer'], 404);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function list_customer(){
        try {
            $search_param = request()->search ?? null;
            $page = request()->page ;
    
            if ($search_param ||  $page) {
                $category = Customer::where('name', 'like', '%' . $search_param . '%')->paginate(5, ['*'], 'page', $page ?? 1);
            }else{
                $customers = Customer::get();
            }
            
            if ($customers->count() > 0) {
                return response()->json([
                    'status'=>200,
                    'data'=>$customers
                ],200);
            }
            else{
                return response()->json([
                    'status'=>404,
                    'data'=>'No records found'
                ],404);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show_customer($id){
      try {
        $customers = Customer::where('id', $id)->first();
        if ($customers) {
            return response()->json([
                'status'=>200,
                'data'=>$customers
            ],200);
        }
        else{
            return response()->json([
                'status'=>404,
                'data'=>'No records found'
            ],404);
        }
      } catch (\Throwable $th) {
        throw $th;
      }  
    }
    
    public function update_customer(Request $request, $id){
        try {
            $payload = [
                'name'=>$request->name,
                'mail'=>$request->mail,
                'phone'=>$request->phone,
                'status'=>$request->status,
            ];

            $customer = Customer::where('id', $id)->update($payload);
            if ($customer) {
                return response()->json([
                    'status'=>200,
                    'data'=>"Customer Updated Sucessfully"
                ],200);
            }
            else{
                return response()->json([
                    'status'=>404,
                    'data'=>'No records found'
                ],404);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function disable_customer($id)
    {
        try {
            $customer = Customer::where('id', $id)->update([
                'status' => 0
            ]);


            return response()->json([
                'status' => 200,
                'message' => "Customer Deactivated Successfully"
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function enable_customer($id)
    {
        try {
            $customer = Customer::where('id', $id)->update([
                'status' => 1
            ]);


            return response()->json([
                'status' => 200,
                'message' => "Customer Activated Successfully"
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function datefilter(Request $request)
    {
        $request->validate([
            'start_date_input' => 'required|date',
            'end_date_input' => 'required|date|after_or_equal:start_date_input',
        ]);

        $startDate = $request->input('start_date_input');
        $endDate = $request->input('end_date_input');

        // Log the actual SQL query being executed
        DB::enableQueryLog();

        $filteredEntries = DB::table('customer_billing')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->get();

        Log::info('Executed Query:', DB::getQueryLog()); // Log query for debugging

        return response()->json([
            'status' => 200,
            'data' => $filteredEntries,
        ]);
    }

    public function getBillDetails($billId)
    {
        try {
            // Fetch the bill from customer_billing
            $bill = DB::table('customer_billing')
                ->where('id', '=', $billId)
                ->first();

            if (!$bill) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Bill not found'
                ], 404);
            }

            // Fetch store details
            $store = DB::table('store')
                ->where('id', '=', $bill->store_id)
                ->first();

            // Fetch bill items — LEFT JOIN both product and inhouse_product
            $billItems = DB::table('customer_product_billing')
                ->leftJoin('product', 'customer_product_billing.productId', '=', 'product.id')
                ->leftJoin('inhouse_product', 'customer_product_billing.inhouse_product_id', '=', 'inhouse_product.id')
                ->leftJoin('brand', 'product.brand_id', '=', 'brand.id')
                ->where('customer_product_billing.cb_id', '=', $billId)
                ->select(
                    'customer_product_billing.*',
                    DB::raw('COALESCE(product.product_name, inhouse_product.product_name) as product_name'),
                    DB::raw('COALESCE(brand.brand_name, "Inhouse") as brand_name')
                )
                ->get();

            // Fetch doctor name
            $doctor = DB::table('doctor')->where('id', '=', $bill->doctor_name)->first();

            // Fetch customer details
            $customer = DB::table('customer')
                ->where('phone', '=', $bill->customer_phone)
                ->first();

            return response()->json([
                'status' => 200,
                'data' => [
                    'bill' => $bill,
                    'items' => $billItems,
                    'customer' => $customer,
                    'store' => $store,
                    'doctor_name' => $doctor?->name ?? 'N/A',
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the bill details',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Same-day, decrease-only correction — not a general bill editor. Sealed
    // (non-inhouse) line items only; quantity can only go down or be removed,
    // and only on the calendar day the bill was created. Restores stock to the
    // exact price-matched batch it was deducted from, same as ReturnController.
    public function editSameDayBilling(Request $request, $billId)
    {
        try {
            $storeMetaId = session('storeId');
            if (!$storeMetaId) {
                return response()->json(['status' => 403, 'message' => 'Store not logged in.'], 403);
            }
            $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
            $storeId = $store?->id;

            $bill = DB::table('customer_billing')->where('id', $billId)->where('store_id', $storeId)->first();
            if (!$bill) {
                return response()->json(['status' => 404, 'message' => 'Bill not found.'], 404);
            }
            if ($bill->billing_date !== now()->format('Y-m-d')) {
                return response()->json(['status' => 400, 'message' => 'This bill can only be edited on the day it was created.'], 400);
            }
            $alreadyReturned = DB::table('credit_note_customer')->where('source_bill_id', $billId)->exists();
            if ($alreadyReturned) {
                return response()->json(['status' => 400, 'message' => 'This bill has already been returned and cannot be edited.'], 400);
            }

            $items = $request->items ?? [];
            if (empty($items)) {
                return response()->json(['status' => 400, 'message' => 'No items to update.'], 400);
            }

            DB::beginTransaction();

            foreach ($items as $entry) {
                $original = DB::table('customer_product_billing')
                    ->where('id', $entry['item_id'] ?? null)
                    ->where('cb_id', $billId)
                    ->first();
                if (!$original) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => 'One of the items was not found on this bill.'], 400);
                }
                if (!empty($original->inhouse_product_id)) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => "Inhouse products ({$original->pack}) cannot be edited."], 400);
                }

                $newQty = (float) ($entry['new_qty'] ?? -1);
                $originalQty = (float) $original->qty;
                if ($newQty < 0 || $newQty > $originalQty) {
                    DB::rollBack();
                    return response()->json(['status' => 400, 'message' => "Quantity can only be decreased for {$original->pack}."], 400);
                }

                $delta = $originalQty - $newQty;
                if ($delta > 0) {
                    $pack = DB::table('pack')->where('pack_name', $original->pack)->first();
                    if ($pack) {
                        $prQuery = DB::table('purchase_request')
                            ->where('product_id', $original->productId)
                            ->where('pack_id', $pack->id);

                        $priceRow = DB::table('price')->where('price_name', $original->unitValue)->first();
                        if ($priceRow) {
                            $prQuery->where('price_id', $priceRow->id);
                        }

                        $pr = $prQuery->orderBy('id')->first();
                        if ($pr) {
                            DB::table('purchase_request')->where('id', $pr->id)->increment('qty', $delta);
                        }
                    }
                }

                if ($newQty == 0) {
                    DB::table('customer_product_billing')->where('id', $original->id)->delete();
                } else {
                    $ratio = $newQty / $originalQty;
                    DB::table('customer_product_billing')->where('id', $original->id)->update([
                        'qty' => $newQty,
                        'totalAmount' => round((float) $original->totalAmount * $ratio, 2),
                        'gstAmount' => $original->gstAmount !== null ? round((float) $original->gstAmount * $ratio, 2) : null,
                        'updated_at' => now(),
                    ]);
                }
            }

            $remaining = DB::table('customer_product_billing')->where('cb_id', $billId)->get();
            $totalAmt = $remaining->sum(fn ($r) => (float) $r->totalAmount);
            $gst = $remaining->sum(fn ($r) => (float) ($r->gstAmount ?? 0));

            DB::table('customer_billing')->where('id', $billId)->update([
                'total_amt' => round($totalAmt, 2),
                'gst' => round($gst, 2),
                'cgst' => round($gst / 2, 2),
                'sgst' => round($gst / 2, 2),
                'edited_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Bill updated successfully.',
                'total_amt' => round($totalAmt, 2),
                'gst' => round($gst, 2),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }

    public function getStoreInfo(Request $request) {
        try {
            $storeId = session('storeId');
            
            if (!$storeId) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Store not logged in.'
                ], 403);
            }

            $store = DB::table('store')
                ->where('store_meta_id', $storeId)
                ->first();

            if (!$store) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Store not found.'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => [
                    'store_name' => $store->name,
                    'store_address' => $store->store_address,
                    'dl_number' => $store->dl_number ?? 'HL-1046-S', // Default fallback
                    'helpline_number' => $store->helpline_number ?? '8100968101', // Default fallback
                    'store_email' => $store->store_mail
                ]
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching store information.',
                'error' => $th->getMessage()
            ], 500);
        }
    }



  

    
}

