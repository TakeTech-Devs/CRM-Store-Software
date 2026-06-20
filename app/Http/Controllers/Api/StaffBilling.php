<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use DB;

class StaffBilling extends Controller
{
    public function createBilling(Request $request){
        try {
            $storeMetaId = session('storeId');

            if (!$storeMetaId) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Store not logged in.'
                ], 403);
            }

            $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
            $storeId = $store?->id;

            $invoiceNo = $this->generateInvoiceNumber($storeId);

            $staff_phone = $request->staff_phone;
            $doctor_name = $request->doctor_name;
            $paymentType = $request->paymentType;
            $product_billing = $request->product_billings;
            $billing_date = $request->billing_date;
            $staff_name = $request->staff_name;
            $billingType = $request->billingType;
            $total_amt = $request->total_amt;
            $gstAmount = $request->gstAmount;
            $cgst = $request->cgst;
            $sgst = $request->sgst;

            DB::beginTransaction();

            foreach ($product_billing as $key => $value) {
                if (filter_var($value['is_inhouse'] ?? false, FILTER_VALIDATE_BOOLEAN)) continue;

                $pr = DB::table('purchase_request')->where('id', $value['purchase_request_id'])->first();

                if (!$pr) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'message' => 'Product / pack size not assigned to store.'
                    ], 400);
                }

                $remainingPrQty = $pr->qty - $value['qty'];
                if ($remainingPrQty < 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 400,
                        'message' => 'Insufficient store-assigned stock for the product.'
                    ], 400);
                }

                DB::table('purchase_request')->where('id', $pr->id)->update([
                    'qty' => $remainingPrQty,
                    'updated_at' => now(),
                ]);
            }

            $insert_cb = DB::table('staff_billing')->insertGetId([
                'store_id' => $storeId,
                'staff_phone' => $staff_phone,
                'staff_name' => $staff_name,
                'doctor_name' => $doctor_name,
                'invoiceNo' => $invoiceNo,
                'paymentType' => $paymentType,
                'billing_date' => $billing_date,
                'billingType' => $billingType,
                'total_amt' => $total_amt,
                'gst' => $gstAmount,
                'cgst' => $cgst,
                'sgst' => $sgst,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($product_billing as $key => $value) {
                DB::table('staff_product_billing')->insert([
                    'category' => $value['category'],
                    'discount' => $value['discount'],
                    'pack' => $value['pack'],
                    'productId' => $value['productId'] ?? null,
                    'inhouse_product_id' => $value['inhouse_product_id'] ?? null,
                    'qty' => $value['qty'],
                    'subCategory' => $value['subCategory'],
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
        $yy  = date('y');
        $mm  = date('m');
        $storeCode = 'S' . str_pad($storeId, 3, '0', STR_PAD_LEFT);

        $count = DB::table('staff_billing')
            ->where('store_id', $storeId)
            ->whereYear('billing_date', date('Y'))
            ->whereMonth('billing_date', date('m'))
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

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
            $query = DB::table('staff_billing');

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


    public function create_staff(Request $request){
        try {
            if (empty($request->stf_id)) {
                return response()->json(['status' => 422, 'data' => 'Company Staff ID is required.'], 422);
            }

            $store    = DB::table('store')->first();
            $storeKey = $store->id ?? 0;

            $payload = [
                'stf_id'     => $request->stf_id,
                'store_id'   => $storeKey,
                'name'       => $request->name,
                'mail'       => $request->mail,
                'phone'      => $request->phone,
                'status'     => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $staff = Staff::insert($payload);
            if ($staff) {
                return response()->json(['status' => 200, 'data' => $request->stf_id], 200);
            }

            return response()->json(['status' => 404, 'data' => 'Failed to create staff'], 404);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function list_staff(){
        try {
            $search_param = request()->search ?? null;
            $page = request()->page ;
    
            if ($search_param ||  $page) {
                $category = Staff::where('name', 'like', '%' . $search_param . '%')->paginate(5, ['*'], 'page', $page ?? 1);
            }else{
                $staffs = Staff::get();
            }
            
            if ($staffs->count() > 0) {
                return response()->json([
                    'status'=>200,
                    'data'=>$staffs
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

    public function show_staff($id){
      try {
        $staffs = Staff::where('id', $id)->first();
        if ($staffs) {
            return response()->json([
                'status'=>200,
                'data'=>$staffs
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
    
    public function update_staff(Request $request, $id){
        try {
            $payload = [
                'name'=>$request->name,
                'mail'=>$request->mail,
                'phone'=>$request->phone,
                'status'=>$request->status,
            ];

            $staff = Staff::where('id', $id)->update($payload);
            if ($staff) {
                return response()->json([
                    'status'=>200,
                    'data'=>"staff Updated Sucessfully"
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

    public function disable_staff($id)
    {
        try {
            $staff = Staff::where('id', $id)->update([
                'status' => 0
            ]);


            return response()->json([
                'status' => 200,
                'message' => "Staff Deactivated Successfully"
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function enable_staff($id)
    {
        try {
            $staff = Staff::where('id', $id)->update([
                'status' => 1
            ]);


            return response()->json([
                'status' => 200,
                'message' => "Staff Activated Successfully"
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

        $filteredEntries = DB::table('staff_billing')
                        ->whereDate('staff_billing.created_at', '>=', $startDate)
                        ->whereDate('staff_billing.created_at', '<=', $endDate)
                        ->get();

        return response()->json([
            'status' => 200,
            'data' => $filteredEntries,
        ]);
    }

    public function getBillDetails($billId)
    {
        try {
            $bill = DB::table('staff_billing')
                ->where('id', '=', $billId)
                ->first();

            if (!$bill) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Bill not found'
                ], 404);
            }

            $store = DB::table('store')
                ->where('id', '=', $bill->store_id)
                ->first();

            $billItems = DB::table('staff_product_billing')
                ->leftJoin('product', 'staff_product_billing.productId', '=', 'product.id')
                ->leftJoin('inhouse_product', 'staff_product_billing.inhouse_product_id', '=', 'inhouse_product.id')
                ->where('staff_product_billing.cb_id', '=', $billId)
                ->select(
                    'staff_product_billing.*',
                    DB::raw('COALESCE(product.product_name, CONCAT("[Inhouse] ", inhouse_product.product_name)) as product_name')
                )
                ->get();

            $doctor = DB::table('doctor')->where('id', '=', $bill->doctor_name)->first();

            return response()->json([
                'status' => 200,
                'data' => [
                    'bill' => $bill,
                    'items' => $billItems,
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

