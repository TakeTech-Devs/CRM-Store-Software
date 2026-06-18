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
            $phone = request()->phone ?? null;
            $dataQuery =  DB::table('customer');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            if ($phone) {
                $dataQuery->where('phone', $phone);
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
            $phone = request()->phone ?? null;
            $dataQuery =  DB::table('staff');
            if ($id) {
                $dataQuery->where('id', $id);
            }
            if ($phone) {
                $dataQuery->where('phone', $phone);
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
                $dataQuery->where('product.id', $id);
            } else {
                // Only fetch products with available stock if no specific ID is requested
                $dataQuery->join('purchase_request', 'product.id', '=', 'purchase_request.product_id')
                          ->where('purchase_request.qty', '>', 0)
                          ->select('product.id', 'product.product_name')
                          ->distinct();
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
                    $packs = DB::table('purchase_request')
                        ->join('pack', 'purchase_request.pack_id', '=', 'pack.id')
                        ->where('purchase_request.product_id', $productId)
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

    public function billingProductOptions() {
        try {
            $data = DB::table('purchase_request')
                ->join('product', 'purchase_request.product_id', '=', 'product.id')
                ->join('pack', 'purchase_request.pack_id', '=', 'pack.id')
                ->join('price', 'purchase_request.price_id', '=', 'price.id')
                ->join('category', 'product.category_id', '=', 'category.id')
                ->join('sub_category', 'product.sub_category_id', '=', 'sub_category.id')
                ->where('purchase_request.qty', '>', 0)
                ->select(
                    'purchase_request.id as purchase_request_id',
                    'purchase_request.qty as avail_qty',
                    'product.id as product_id',
                    'product.product_name',
                    'product.gst',
                    'pack.id as pack_id',
                    'pack.pack_name',
                    'price.id as price_id',
                    'price.price_name',
                    'product.brand_id',
                    'category.category_name',
                    'sub_category.sub_category_name'
                )
                ->orderBy('product.product_name')
                ->orderBy('pack.pack_name')
                ->orderBy('price.price_name')
                ->get();

            return response()->json(['status' => 200, 'data' => $data], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getStores(Request $request)
    {
        try {
            $storeMetaId = session('storeId');
            $localStore  = $storeMetaId ? DB::table('store')->where('store_meta_id', $storeMetaId)->first() : null;

            $query = DB::connection('remote_mysql')
                ->table('store')
                ->where('store_status', 1)
                ->select('id', 'name', 'store_address');

            if ($localStore) {
                $query->where('id', '!=', $localStore->id);
            }

            $stores = $query->orderBy('name')->get();

            return response()->json(['status' => 200, 'data' => $stores], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => $th->getMessage()], 500);
        }
    }
}
