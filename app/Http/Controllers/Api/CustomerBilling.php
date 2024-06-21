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
            $billingType = $request->billingType;
            $total_amt = $request->total_amt;

            $insert_cb =  DB::table('curtomer_billing')->insertGetId([
                'customer_phone'=>$customer_phone,
                'customer_name'=>$customer_name,
                'doctor_name'=>$doctor_name,
                'invoiceNo'=>$invoiceNo,
                'paymentType'=>$paymentType,
                'biilling_date'=>$biilling_date,
                'billingType'=>$billingType,
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

    public function listBilling(Request $request){
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $search = $request->query('search');
            $page = $request->query('page') ;
            $limit = $request->query('limit');
            $billingType = $request->billingType;
            $query = DB::table('curtomer_billing');

            if ($billingType) {
                $query->where('billingType', '=', $billingType);
            }
            
            if ($startDate) {
                $query->where('biilling_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('biilling_date', '<=', $endDate);
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
}
