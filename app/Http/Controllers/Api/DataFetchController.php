<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Session;


class DataFetchController extends Controller
{
    public function dataFetch(Request $request, $storeId)
    {
        try {

            $remoteDatabrand = DB::connection('remote_mysql')->table('brand')->get();
            foreach ($remoteDatabrand as $key => $value) {
                $getBrand = DB::table('brand')->where(['brand_name' => $value->brand_name])->first();
                // dd($getBrand);
                if ($getBrand) {
                    DB::table('brand')->where('id', $getBrand->id)->update([
                        'brand_name' => $value->brand_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('brand')->insert([
                        'brand_name' => $value->brand_name,
                        'status' => $value->status
                    ]);
                }
            }
            $remoteDatacategory = DB::connection('remote_mysql')->table('category')->get();
            foreach ($remoteDatacategory as $key => $value) {
                $getCategory = DB::table('category')->where(['category_name' => $value->category_name,])->first();
                if ($getCategory) {
                    DB::table('category')->where('id', $getCategory->id)->update([
                        'category_name' => $value->category_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('category')->insert([
                        'category_name' => $value->category_name,
                        'status' => $value->status
                    ]);
                }
            }
            $remoteDatasub_category = DB::connection('remote_mysql')->table('sub_category')->get();
            foreach ($remoteDatasub_category as $key => $value) {
                $getSubCategory =  DB::table('sub_category')->where(['sub_category_name' => $value->sub_category_name,])->first();
                if ($getSubCategory) {
                    DB::table('sub_category')->where('id', $getSubCategory->id)->where([
                        'category_id' => $value->category_id,
                        'sub_category_name' => $value->sub_category_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('sub_category')->insert([
                        'category_id' => $value->category_id,
                        'sub_category_name' => $value->sub_category_name,
                        'status' => $value->status
                    ]);
                }
            }

            $remoteDataproduct = DB::connection('remote_mysql')->table('product')->get();
            foreach ($remoteDataproduct as $key => $value) {
                $getSubCategory =  DB::table('product')->where(['product_name' => $value->product_name])->first();

                if ($getSubCategory) {
                    DB::table('product')->where('id', $getSubCategory->id)->update([
                        'product_name' => $value->product_name,
                        'brand_id' => $value->brand_id,
                        'category_id' => $value->category_id,
                        'sub_category_id' => $value->sub_category_id,
                        'hsn_code' => $value->hsn_code,
                        'gst' => $value->gst,
                        'status' => $value->status,
                    ]);
                } else {
                    DB::table('product')->insert([
                        'product_name' => $value->product_name,
                        'brand_id' => $value->brand_id,
                        'category_id' => $value->category_id,
                        'sub_category_id' => $value->sub_category_id,
                        'hsn_code' => $value->hsn_code,
                        'gst' => $value->gst,
                        'status' => $value->status,
                    ]);
                }
            }
            $remoteDatasupplier = DB::connection('remote_mysql')->table('supplier')->get();
            foreach ($remoteDatasupplier as $key => $value) {
                $getSupplier =  DB::table('supplier')->where(['supplier_name' => $value->supplier_name])->first();
                if ($getSupplier) {
                    DB::table('supplier')->where('id',  $getSupplier->id)->update([
                        'supplier_name' => $value->supplier_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('supplier')->insert([
                        'supplier_name' => $value->supplier_name,
                        'status' => $value->status
                    ]);
                }
            }
            $remoteDatapack = DB::connection('remote_mysql')->table('pack')->get();
            foreach ($remoteDatapack as $key => $value) {
                $getPack =  DB::table('pack')->where(['pack_name' => $value->pack_name])->first();
                if ($getPack) {
                    DB::table('pack')->where('id', $getPack->id)->update([
                        'pack_name' => $value->pack_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('pack')->insert([
                        'pack_name' => $value->pack_name,
                        'status' => $value->status
                    ]);
                }
            }
            $remoteDataprice = DB::connection('remote_mysql')->table('price')->get();
            foreach ($remoteDataprice as $key => $value) {
                $getPrice =  DB::table('price')->where(['price_name' => $value->price_name])->first();
                if ($getPrice) {
                    DB::table('price')->where('id', $getPrice->id)->update([
                        'price_name' => $value->price_name
                    ]);
                } else {
                    DB::table('price')->insert([
                        'price_name' => $value->price_name
                    ]);
                }
            }
            // dd($request->session()->get('storeId'));
            $store_meta_id = $request->session()->get('storeId');
            $remoteDataStore = DB::connection('remote_mysql')->table('store')-> where('store_meta_id', $store_meta_id)->first();

            $remoteDatastore_assign = DB::connection('remote_mysql')->table('store_assign')-> where('store_id', $remoteDataStore->id)->get();
            foreach ($remoteDatastore_assign as $key => $value) {
                $getStoreAssign =  DB::table('store_assign')->where(['assign_bill_number' => $value->assign_bill_number,])->first();
                if ($getStoreAssign) {
                    DB::table('store_assign')->where('id', $getStoreAssign->id)->update([
                        // 'purchase_stock_id' => 1,
                        'store_id' => $value->store_id,
                        'assign_bill_number' => $value->assign_bill_number,
                        'total' => $value->total
                    ]);
                } else {
                    DB::table('store_assign')->insert([
                        // 'purchase_stock_id' => 1,
                        'store_id' => $value->store_id,
                        'assign_bill_number' => $value->assign_bill_number,
                        'total' => $value->total
                    ]);
                }
            }

            $remoteDatapurchase_request = DB::connection('remote_mysql')->table('purchase_request')->where('store_assign_id', $remoteDataStore->id)->get();
            foreach ($remoteDatapurchase_request as $key => $value) {
                $getStoreAssign =  DB::table('purchase_request')->where([
                    'store_assign_id' => $value->store_assign_id,
                    'brand_id' => $value->brand_id,
                    'product_id' => $value->product_id
                ])->first();

                if ($getStoreAssign) {
                    DB::table('purchase_request')->where('id', $getStoreAssign->id)->update([
                        // 'store_assign_id' => $value->store_assign_id,
                        'brand_id' => $value->brand_id,
                        'product_id' => $value->product_id,
                        'pack_id' => $value->pack_id,
                        'price_id' => $value->price_id,
                        'qty' => $value->qty,
                        'qty_left' => $value->qty_left,
                        'exp_date' => $value->exp_date,
                    ]);
                } else {
                    DB::table('purchase_request')->insert([
                        'store_assign_id' => $value->store_assign_id,
                        'brand_id' => $value->brand_id,
                        'product_id' => $value->product_id,
                        'pack_id' => $value->pack_id,
                        'price_id' => $value->price_id,
                        'qty' => $value->qty,
                        'qty_left' => $value->qty_left,
                        'exp_date' => $value->exp_date,
                    ]);
                }
            }

            $remoteDataCustomer = DB::connection('remote_mysql')->table('customer')->get();
            foreach ($remoteDataCustomer as $key => $value) {
                $getCustomer =  DB::table('customer')->where([
                    'name' => $value->name,
                    'mail' => $value->mail,
                    'phone' => $value->phone,
                    'status' => $value->status,
                ])->first();

                if ($getCustomer) {
                    DB::table('customer')->where('id', $getCustomer->id)->update([
                        'name' => $value->name,
                        'mail' => $value->mail,
                        'phone' => $value->phone,
                        'status' => $value->status,
                    ]);
                }else{
                    DB::table('customer')->insert([
                        'name' => $value->name,
                        'mail' => $value->mail,
                        'phone' => $value->phone,
                        'status' => $value->status,
                    ]);
                }
            }

            $remoteDataDoctor = DB::connection('remote_mysql')->table('doctor')->get();
            foreach ($remoteDataDoctor as $key => $value) {
                $getDoctor =  DB::table('doctor')->where([
                    'name' => $value->name,
                    'mail' => $value->mail,
                    'phone' => $value->phone,
                    'degree' => $value->degree,
                    'status' => $value->status
                ])->first();

                if ($getDoctor) {
                    DB::table('doctor')->where('id', $getDoctor->id)->update([
                        'name' => $value->name,
                        'mail' => $value->mail,
                        'phone' => $value->phone,
                        'degree' => $value->degree,
                        'status' => $value->status
                    ]);
                }else{
                    DB::table('doctor')->insert([
                        'name' => $value->name,
                        'mail' => $value->mail,
                        'phone' => $value->phone,
                        'degree' => $value->degree,
                        'status' => $value->status
                    ]);
                }
            }
            DB::table('sync_history')->insert([
                'sync_date' => date('Y-m-d'),
                'sync_status' => 'Succeed'
            ]);
            return response()->json([
                'status' => 200,
                'resStatus' => true,
            ], 200);

        } catch (\Throwable $th) {
            DB::table('sync_history')->insert([
                'sync_date' => date('Y-m-d'),
                'sync_status' => "Succeed"
            ]);
            throw $th;
        }
    }

    public function insertStore(Request $request)
    {
        try {
            $payload = [
                "name" => $request->name,
                "store_address" => $request->store_address,
                "store_mail" => $request->store_mail,
                "store_start_date" => $request->store_start_date,
                "store_meta_id" => $request->store_meta_id,
                "store_pass_key" => $request->store_pass_key,
                "store_status" =>$request->store_status,
                "store_verify_status" => 1
            ];

            $insertStore = DB::table('store')->insert($payload);
            if ( $insertStore) {
                return response()->json([
                    'status' => 200,
                    'message' => "Store insert.",
                    'resStatus' => true,
                    // 'data'=> $checkStore
                ], 200);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function checkStore() {
        try {
            $getStore = DB::table('store')->get();

            if (count($getStore) > 0) {
                return response()->json([
                    'status' => 200,
                    'resStatus' => true,
                    'data'=> $getStore
                ], 200);
            }else{
                return response()->json([
                    'status' => 400,
                    'resStatus' => false,
                    // 'data'=> $checkStore
                ], 200);
            }

          
        } catch (\Throwable $th) {
            throw $th;
        }


    }

    public function getSyncHist(Request $request){
        try { 
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $search = $request->query('search');
            $page = $request->query('page') ;
            $limit = $request->query('limit');
            $query = DB::table('sync_history');
            // $history = DB::table('sync_history')->get();
            if ($startDate) {
                $query->where('sync_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('sync_date', '<=', $endDate);
            }
            if ($page && $limit ) { 
                $history = $query->paginate($limit, ['*'], 'page', $page ?? 1);
                
            }else{

                $history = $query->get();
            }

            if ($history->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'data' => $history
                ], 200);
            } else {
                return response()->json([
                    'status' => 404,
                    'data' => 'No records found'
                ], 200);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
