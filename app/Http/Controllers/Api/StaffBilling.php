<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            ]);
            foreach ($product_billing as $key => $value) {
                DB::table('staff_product_billing')->insert([
                    'category' => $value['category'],
                    'discount' => $value['discount'],
                    'mrp' => $value['mrp'],
                    'pack' => $value['pack'],
                    'productId' => $value['productId'],
                    'qty' => $value['qty'],
                    'subCategory' => $value['subCategory'],
                    'totalAmount' => $value['totalAmount'],
                    'unitValue' => $value['unitValue'],
                    'cb_id' =>  $insert_cb
       
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
}
