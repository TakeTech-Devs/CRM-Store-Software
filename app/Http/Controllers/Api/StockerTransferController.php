<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class StockerTransferController extends Controller
{
    public function transfer(Request $request) {
        $stock_id = $request->stock_id; //  $request->stock_id
        $reuested_id = $request->reuested_id; // to transfer 
        $requester_id = $request->requester_id; // from transfer
        $qty = $request->qty;


        $stocks = DB::table('purchase_request')->where('id', $stock_id )->first();

        if ($stocks) {
            DB::table('purchase_request')->where('id', $stock_id )->update([
                'qty' => $stocks->qty
            ]);
            
            $price = DB::connection('remote_mysql')->table('price')->where('id', $stocks->price_id)->first();
            $store_assign = DB::connection('remote_mysql')->table('store_assign')->where('id', $stocks->store_assign_id)->first();
            $store_assign_insert = DB::connection('remote_mysql')->table('store_assign')->insertGetId([
                "purchase_stock_id" => $store_assign->purchase_stock_id,
                "store_id" => $reuested_id,
                "assign_bill_number"=>$store_assign->assign_bill_number,
                "total"=>($price->price_name * $qty)
            ]);

            $admin_purchase_request = DB::connection('remote_mysql')->table('purchase_request')->insert([
                "store_assign_id" => $store_assign_insert,
                "brand_id" => $stocks->brand_id,
                "product_id" =>$stocks->product_id,
                "pack_id" => $stocks->pack_id,
                "price_id" => $stocks->price_id,
                "qty" => $qty,
                "qty_left" => $stocks->qty_left,
                "exp_date" => $stocks->exp_date,
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => "Data transfer to Admin Database"
        ], 200); 
    }
}
