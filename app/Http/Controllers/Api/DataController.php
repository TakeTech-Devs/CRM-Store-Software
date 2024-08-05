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
            $id = request()->id ?? null;
            $dataQuery =  DB::table('purchase_request');
            if ($id) {
                $dataQuery->where('product_id', $id);
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
}
