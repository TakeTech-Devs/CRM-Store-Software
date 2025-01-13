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
        // Ensure the user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => 'Authentication required.'
            ], 401);
        }

        // Retrieve storeId from the session (set during login)
        $storeId = $request->session()->get('storeId');

        // Ensure storeId exists in the session
        if (!$storeId) {
            return response()->json([
                'status' => 403,
                'message' => 'Store not logged in.'
            ], 403);
        }

        // Billing and product details from the request
        $customer_phone = $request->customer_phone;
        $doctor_name = $request->doctor_name;
        $invoiceNo = $request->invoiceNo;
        $paymentType = $request->paymentType;
        $product_billing = $request->product_billings;
        $billing_date = $request->billing_date;
        $customer_name = $request->customer_name;
        $billingType = $request->billingType;
        $total_amt = $request->total_amt;

        DB::beginTransaction();

        // Loop through product billing and check stock
        foreach ($product_billing as $key => $value) {
            $product = DB::table('purchase_stock_entry')->where('product_id', $value['productId'])->first();
            $remainingQty = $product->qty - $value['qty'];

            if ($remainingQty < 0) {
                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'message' => 'Insufficient stock for the product.'
                ], 400);
            }

            DB::table('purchase_stock_entry')->where('product_id', $value['productId'])->update([
                'qty' => $remainingQty,
                'updated_at' => now(),
            ]);
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
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert into customer_product_billing table
        foreach ($product_billing as $key => $value) {
            DB::table('customer_product_billing')->insert([
                'category' => $value['category'],
                'discount' => $value['discount'],
                'pack' => $value['pack'],
                'productId' => $value['productId'],
                'qty' => $value['qty'],
                'subCategory' => $value['subCategory'],
                'totalAmount' => $value['totalAmount'],
                'unitValue' => $value['unitValue'],
                'cb_id' => $insert_cb,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 200,
            'data' => 'Success'
        ], 200);
    } catch (\Throwable $th) {
        DB::rollBack();
        throw $th;
    }
}




    public function listBilling(Request $request){
        
        try {
            

            $getStore = DB::table('store')->get();

            var_dump($getStore);
            
            


            

            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $search = $request->query('search');
            $page = $request->query('page') ;
            $limit = $request->query('limit');
            $billingType = $request->billingType;
            $query = DB::table('customer_billing');

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

            // if ($billing_details->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $billing_details
                ], 200);
            // } else {
            //     return response()->json([
            //         'status' => 404,
            //         'data' => 'No records found'
            //     ], 404);
            // }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create_customer(Request $request){
        try {
            $payload = [
                'name'=>$request->name,
                'mail'=>$request->mail,
                'phone'=>$request->phone,
                'status'=>$request->status,
            ];

            $customer = Customer::insert($payload);
            if ($customer) {
                return response()->json([
                    'status'=>200,
                    'data'=>$customer
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

        $filteredEntries = DB::table('customer_billing')
                        ->whereDate('customer_billing.created_at', '>=', $startDate)
                        ->whereDate('customer_billing.created_at', '<=', $endDate)
                        ->get();

        return response()->json([
            'status' => 200,
            'data' => $filteredEntries,
        ]);
    }

    public function getBillDetails($billId)
    {
        try {
            $bill = DB::table('customer_billing')
                ->where('id', '=', $billId)
                ->first();

            if (!$bill) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Bill not found'
                ], 404);
            }

            $billItems = DB::table('customer_product_billing')
                ->where('cb_id', '=', $billId)
                ->get();

            $customer = DB::table('customer')
                ->where('id', '=', $bill->id)
                ->first();

            return response()->json([
                'status' => 200,
                'data' => [
                    'bill' => $bill,
                    'items' => $billItems,
                    'customer' => $customer
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

    
}
