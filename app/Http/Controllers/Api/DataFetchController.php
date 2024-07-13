<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class DataFetchController extends Controller
{
    public function dataFetch(Request $request, $storeId)
    {
        DB::beginTransaction();
        try {
            // Get the current highest IDs for each table
            $currentBrandId = DB::table('brand')->max('id');
            $currentCategoryId = DB::table('category')->max('id');
            $currentSubCategoryId = DB::table('sub_category')->max('id');
            $currentProductId = DB::table('product')->max('id');
            $currentSupplierId = DB::table('supplier')->max('id');
            $currentPackId = DB::table('pack')->max('id');
            $currentPriceId = DB::table('price')->max('id');
            $currentStoreAssignId = DB::table('store_assign')->max('id');
            $currentPurchaseRequestId = DB::table('purchase_request')->max('id');
            $currentCustomerId = DB::table('customer')->max('id');
            $currentDoctorId = DB::table('doctor')->max('id');
    
            // Fetch remote data and update or insert into the local database
            $remoteDatabrand = DB::connection('remote_mysql')->table('brand')->get();
            foreach ($remoteDatabrand as $value) {
                $getBrand = DB::table('brand')->where(['brand_name' => $value->brand_name])->first();
                if ($getBrand) {
                    DB::table('brand')->where('id', $getBrand->id)->update([
                        'brand_name' => $value->brand_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('brand')->insert([
                        'id' => ++$currentBrandId,
                        'brand_name' => $value->brand_name,
                        'status' => $value->status
                    ]);
                }
            }
    
            $remoteDatacategory = DB::connection('remote_mysql')->table('category')->get();
            foreach ($remoteDatacategory as $value) {
                $getCategory = DB::table('category')->where(['category_name' => $value->category_name])->first();
                if ($getCategory) {
                    DB::table('category')->where('id', $getCategory->id)->update([
                        'category_name' => $value->category_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('category')->insert([
                        'id' => ++$currentCategoryId,
                        'category_name' => $value->category_name,
                        'status' => $value->status
                    ]);
                }
            }
    
            $remoteDatasub_category = DB::connection('remote_mysql')->table('sub_category')->get();
            foreach ($remoteDatasub_category as $value) {
                $getSubCategory = DB::table('sub_category')->where(['sub_category_name' => $value->sub_category_name])->first();
                if ($getSubCategory) {
                    DB::table('sub_category')->where('id', $getSubCategory->id)->update([
                        'category_id' => $value->category_id,
                        'sub_category_name' => $value->sub_category_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('sub_category')->insert([
                        'id' => ++$currentSubCategoryId,
                        'category_id' => $value->category_id,
                        'sub_category_name' => $value->sub_category_name,
                        'status' => $value->status
                    ]);
                }
            }
    
            $remoteDataproduct = DB::connection('remote_mysql')->table('product')->get();
            foreach ($remoteDataproduct as $value) {
                $getSubCategory = DB::table('product')->where(['product_name' => $value->product_name])->first();
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
                        'id' => ++$currentProductId,
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
            foreach ($remoteDatasupplier as $value) {
                $getSupplier = DB::table('supplier')->where(['supplier_name' => $value->supplier_name])->first();
                if ($getSupplier) {
                    DB::table('supplier')->where('id', $getSupplier->id)->update([
                        'supplier_name' => $value->supplier_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('supplier')->insert([
                        'id' => ++$currentSupplierId,
                        'supplier_name' => $value->supplier_name,
                        'status' => $value->status
                    ]);
                }
            }
    
            $remoteDatapack = DB::connection('remote_mysql')->table('pack')->get();
            foreach ($remoteDatapack as $value) {
                $getPack = DB::table('pack')->where(['pack_name' => $value->pack_name])->first();
                if ($getPack) {
                    DB::table('pack')->where('id', $getPack->id)->update([
                        'pack_name' => $value->pack_name,
                        'status' => $value->status
                    ]);
                } else {
                    DB::table('pack')->insert([
                        'id' => ++$currentPackId,
                        'pack_name' => $value->pack_name,
                        'status' => $value->status
                    ]);
                }
            }
    
            $remoteDataprice = DB::connection('remote_mysql')->table('price')->get();
            foreach ($remoteDataprice as $value) {
                $getPrice = DB::table('price')->where(['price_name' => $value->price_name])->first();
                if ($getPrice) {
                    DB::table('price')->where('id', $getPrice->id)->update([
                        'price_name' => $value->price_name
                    ]);
                } else {
                    DB::table('price')->insert([
                        'id' => ++$currentPriceId,
                        'price_name' => $value->price_name
                    ]);
                }
            }
    
            // Fetch store data
            $store_meta_id = $request->session()->get('storeId');
            $remoteDataStore = DB::connection('remote_mysql')->table('store')->where('store_meta_id', $store_meta_id)->first();
    
            $remoteDatastore_assign = DB::connection('remote_mysql')->table('store_assign')->where('store_id', $remoteDataStore->id)->get();
            foreach ($remoteDatastore_assign as $value) {
                $getStoreAssign = DB::table('store_assign')->where(['assign_bill_number' => $value->assign_bill_number])->first();
                if ($getStoreAssign) {
                    DB::table('store_assign')->where('id', $getStoreAssign->id)->update([
                        'store_id' => $value->store_id,
                        'assign_bill_number' => $value->assign_bill_number,
                        'total' => $value->total
                    ]);
                } else {
                    $sa_id = DB::table('store_assign')->insert([
                        'id' => ++$currentStoreAssignId,
                        'store_id' => $value->store_id,
                        'assign_bill_number' => $value->assign_bill_number,
                        'total' => $value->total
                    ]);
                }


                $remoteDatapurchase_request = DB::connection('remote_mysql')->table('purchase_request')->where('store_assign_id', $getStoreAssign->id)->get();
                // dd($remoteDatapurchase_request);
                foreach ($remoteDatapurchase_request as $value) {
                    $getStoreAssign = DB::table('purchase_request')->where([
                        'store_assign_id' => $value->store_assign_id,
                        'brand_id' => $value->brand_id,
                        'product_id' => $value->product_id
                    ])->first();
                    if ($getStoreAssign) {
                        DB::table('purchase_request')->where('id', $getStoreAssign->id)->update([
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
                            'id' => ++$currentPurchaseRequestId,
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
            }
    
            
           
    
            $remoteDataCustomer = DB::connection('remote_mysql')->table('customer')->get();
            foreach ($remoteDataCustomer as $value) {
                $getCustomer = DB::table('customer')->where([
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
                } else {
                    DB::table('customer')->insert([
                        'id' => ++$currentCustomerId,
                        'name' => $value->name,
                        'mail' => $value->mail,
                        'phone' => $value->phone,
                        'status' => $value->status,
                    ]);
                }
            }
    
            $remoteDataDoctor = DB::connection('remote_mysql')->table('doctor')->get();
            foreach ($remoteDataDoctor as $value) {
                $getDoctor = DB::table('doctor')->where([
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
                } else {
                    DB::table('doctor')->insert([
                        'id' => ++$currentDoctorId,
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
    
            DB::commit();
            return response()->json([
                'status' => 200,
                'resStatus' => true,
            ], 200);
    
        } catch (\Throwable $th) {
            DB::rollback();
            DB::table('sync_history')->insert([
                'sync_date' => date('Y-m-d'),
                'sync_status' => 'Failed'
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
        
    public function backupSQL ()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $port = env('DB_PORT');

        $fileName = "backup-" . date('Y-m-d_H-i-s') . ".sql";
        $relativeFilePath = "backup/{$fileName}";
        $filePath = storage_path("app/public/{$relativeFilePath}");

        if (!file_exists(storage_path('app/public/backup'))) {
            mkdir(storage_path('app/public/backup'), 0777, true);
        }

        $command = "mysqldump --user={$username} --password={$password} --host={$host} --port={$port} {$database} > {$filePath}";

        $result = null;
        $output = null;
        exec($command, $output, $result);

        if ($result !== 0) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create backup.'], 500);
        }

        $fileUrl = url("storage/{$relativeFilePath}");
            DB::table('backup')->insert([
                'date' => date('Y-m-d H:i:s'),
                'file_path' => $fileUrl,
                'file_name' => $fileName
            ]);
    
            return response()->json(['status' => 'success', 'file' => $fileName, 'file_url' => $fileUrl]);
    }

    public function deleteBackup($id)
    {
        $backup = DB::table('backup')->where('id', $id)->first();

        if (!$backup) {
            return response()->json(['status' => 'error', 'message' => 'Backup not found.'], 404);
        }
            $relativeFilePath = "public/backup/{$backup->file_name}";
            if (Storage::exists($relativeFilePath)) {
                Storage::delete($relativeFilePath);
            } else {
                return response()->json(['status' => 'error', 'message' => 'File not found.'], 404);
            }
        DB::table('backup')->where('id', $id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Backup File deleted successfully.']);
    }

    public function getBackup(){
        $backupFile = DB::table('backup')->get();
        return response()->json(['status' => 'success', 'data' => $backupFile]);

    }

    public function purchase_request_all(){
        try {
            $purchase_request = DB::table('purchase_request')->get();

        return response()->json(['status' => 'success', 'purchase_request' => $purchase_request]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
