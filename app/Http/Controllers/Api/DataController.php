<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DataController extends Controller
{
    public function customer_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('customer');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public function staff_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('staff');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public function doctor_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('doctor');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public function purchase_bill(){
        try {
            $productId = request()->product_id ?? null;
            $packId = request()->pack_id ?? null;

            $dataQuery =  DB::table('purchase_request');

            if ($productId) {
                $dataQuery->where('product_id', $productId);
            }
            if ($packId) {
                $dataQuery->where('pack_id', $packId);
            }

            $data = $dataQuery->get();

            return response()->json([
                'status' => 200,
                'purchase_request' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }

    }
    public function product_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('product');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public function brand_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('brand');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public function category_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('category');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public function sub_category_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('sub_category');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    public function pack_data(){
        try {
            $id = request()->id ?? null;
            $dataQuery =  DB::table('pack');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            $data = $dataQuery->get();
            return response()->json([
                'status' => 200,
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
        public function price_data(){
            try {
                $id = request()->id ?? null;
                $dataQuery =  DB::table('price');
                if ($id) {
                    $dataQuery->where('id', $id);
                }
                $data = $dataQuery->get();
                return response()->json([
                    'status' => 200,
                    'data' => $data
                ], 200);
            } catch (\Throwable $th) {
                throw $th;
            }
            
        }
    
            public function packs_by_product($productId){
                try {
                    $packs = DB::table('purchase_stock_entry')
                        ->join('pack', 'purchase_stock_entry.pack_id', '=', 'pack.id')
                        ->where('purchase_stock_entry.product_id', $productId)
                        ->select('pack.id', 'pack.pack_name')
                        ->distinct()
                        ->get();
        
                    return response()->json([
                        'status' => 200,
                        'data' => $packs
                    ], 200);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }        
    }
