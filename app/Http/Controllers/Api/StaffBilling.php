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
            $staff_phone = $request->staff_phone;
            $doctor_name = $request->doctor_name;
            $invoiceNo = $request->invoiceNo;
            $paymentType = $request->paymentType;
            $product_billing = $request->product_billings;
            $billing_date = $request->billing_date;
            $staff_name = $request->staff_name;
            $total_amt = $request->total_amt;

            $insert_cb =  DB::table('staff_billing')->insertGetId([
                'staff_phone'=>$staff_phone,
                'staff_name'=>$staff_name,
                'doctor_name'=>$doctor_name,
                'invoiceNo'=>$invoiceNo,
                'paymentType'=>$paymentType,
                'billing_date'=>$billing_date,
                'total_amt'=>$total_amt,
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
            foreach ($product_billing as $key => $value) {
                DB::table('staff_product_billing')->insert([
                    'category' => $value['category'],
                    'discount' => $value['discount'],
                    'pack' => $value['pack'],
                    'productId' => $value['productId'],
                    'qty' => $value['qty'],
                    'subCategory' => $value['subCategory'],
                    'totalAmount' => $value['totalAmount'],
                    'unitValue' => $value['unitValue'],
                    'cb_id' =>  $insert_cb,
                    'created_at'=>now(),
                    'updated_at'=>now(),
       
                ]);
                
            }
            return response()->json([
                'status'=>200,
                'data'=>"Success"
            ],200);
        } catch (\Throwable $th) {
            throw $th;
        }
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
            $payload = [
                'name'=>$request->name,
                'mail'=>$request->mail,
                'phone'=>$request->phone,
                'status'=>$request->status,
            ];

            $staff = Staff::insert($payload);
            if ($staff) {
                return response()->json([
                    'status'=>200,
                    'data'=>$staff
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
}
