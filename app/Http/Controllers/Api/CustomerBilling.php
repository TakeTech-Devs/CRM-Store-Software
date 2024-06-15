<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CustomerBilling extends Controller
{
    public function createBilling(Request $request){
        try {
            $customer_phone = $request->customer_phone;
            $doctor_name = $request->doctor_name;
            $invoiceNo = $request->invoiceNo;
            $paymentType = $request->paymentType;
            $product_billing = $request->product_billings;
            $biilling_date = $request->biilling_date;
            $customer_name = $request->customer_name;
            $total_amt = $request->total_amt;

            $insert_cb =  DB::table('curtomer_billing')->insertGetId([
                'customer_phone'=>$customer_phone,
                'customer_name'=>$customer_name,
                'doctor_name'=>$doctor_name,
                'invoiceNo'=>$invoiceNo,
                'paymentType'=>$paymentType,
                'biilling_date'=>$biilling_date,
                'total_amt'=>$total_amt,
            ]);

            foreach ($product_billing as $key => $value) {
                DB::table('curtomer_product_billing')->insert([
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
