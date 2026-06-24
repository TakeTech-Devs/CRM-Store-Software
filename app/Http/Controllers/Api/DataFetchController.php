<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Store;
use App\Models\SubCategory;
use App\Models\Supplier;
use App\Models\Pack;
use App\Models\Price;
use App\Models\Doctor;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;


class DataFetchController extends Controller
{
    public function dataFetch(Request $request, $storeId)
    {
        return $this->syncInFromAdmin($request, $storeId);

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
            $currentStoreId = DB::table('store')->max('id');


            // Fetch remote data and update or insert into the local database
            $remoteDatabrand = DB::connection('remote_mysql')->table('brand')->get();
            foreach ($remoteDatabrand as $value) {

                if(Brand::where('id', $value->id)->doesntExist()){
                    DB::table('brand')->insert([
                        'id' => $value->id,
                        'brand_name' => $value->brand_name,
                        'status' => $value->status
                    ]);
                }
            }

            $remoteDatacategory = DB::connection('remote_mysql')->table('category')->get();
            foreach ($remoteDatacategory as $value) {

                if(Category::where('id', $value->id)->doesntExist()){
                    DB::table('category')->insert([
                        'id' => $value->id,
                        'category_name' => $value->category_name,
                        'status' => $value->status
                    ]);
                }
            }

            $remoteDatasub_category = DB::connection('remote_mysql')->table('sub_category')->get();
            foreach ($remoteDatasub_category as $value) {

                if(SubCategory::where('id', $value->id)->doesntExist()){
                    DB::table('sub_category')->insert([
                        'id' => $value->id,
                        'category_id' => $value->category_id,
                        'sub_category_name' => $value->sub_category_name,
                        'status' => $value->status
                    ]);
                }
            }

            $remoteDataproduct = DB::connection('remote_mysql')->table('product')->get();
            foreach ($remoteDataproduct as $value) {
                if(!Product::where('id', $value->id)->exists()){
                    DB::table('product')->insert([
                        'id' => $value->id,
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

                if(Supplier::where('id', $value->id)->doesntExist()){
                    DB::table('supplier')->insert([
                        'id' => $value->id,
                        'supplier_name' => $value->supplier_name,
                        'status' => $value->status
                    ]);
                }

                // $getSupplier = DB::table('supplier')->where(['supplier_name' => $value->supplier_name])->first();
                // if ($getSupplier) {
                //     DB::table('supplier')->where('id', $getSupplier->id)->update([
                //         'supplier_name' => $value->supplier_name,
                //         'status' => $value->status
                //     ]);
                // } else {
                //     DB::table('supplier')->insert([
                //         'id' => ++$currentSupplierId,
                //         'supplier_name' => $value->supplier_name,
                //         'status' => $value->status
                //     ]);
                // }
            }

            $remoteDatapack = DB::connection('remote_mysql')->table('pack')->get();
            foreach ($remoteDatapack as $value) {
                if(!Pack::where('id', $value->id)->exists()){
                    DB::table('pack')->insert([
                        'id' => $value->id,
                        'pack_name' => $value->pack_name,
                        'status' => $value->status
                    ]);
                }
                // $getPack = DB::table('pack')->where(['pack_name' => $value->pack_name])->first();
                // if ($getPack) {
                //     DB::table('pack')->where('id', $getPack->id)->update([
                //         'pack_name' => $value->pack_name,
                //         'status' => $value->status
                //     ]);
                // } else {
                //     DB::table('pack')->insert([
                //         'id' => ++$currentPackId,
                //         'pack_name' => $value->pack_name,
                //         'status' => $value->status
                //     ]);
                // }
            }

            $remoteDataprice = DB::connection('remote_mysql')->table('price')->get();
            foreach ($remoteDataprice as $value) {

                if(!Price::where('id', $value->id)->exists()){
                    DB::table('price')->insert([
                        'id' => $value->id,
                        'price_name' => $value->price_name
                    ]);
                }
                // $getPrice = DB::table('price')->where(['price_name' => $value->price_name])->first();
                // if ($getPrice) {
                //     DB::table('price')->where('id', $getPrice->id)->update([
                //         'price_name' => $value->price_name
                //     ]);
                // } else {
                //     DB::table('price')->insert([
                //         'id' => ++$currentPriceId,
                //         'price_name' => $value->price_name
                //     ]);
                // }
            }

            // // Fetch store data
            $store_meta_id = session('storeId');
            $remoteDataStore = DB::connection('remote_mysql')->table('store')->where('store_meta_id', $store_meta_id)->first();

            $remoteDatastore_assign = DB::connection('remote_mysql')->table('store_assign')->where('store_id', $remoteDataStore->id)->get();
            // return $remoteDatastore_assign;
            foreach ($remoteDatastore_assign as $value) {
                $getStoreAssign = DB::table('store_assign')->where(['assign_bill_number' => $value->assign_bill_number])->first();
                // dd($value)
                if ($value->purchase_stock_id) {
                    $remoteData_purchase_stock = DB::connection('remote_mysql')->table('purchase_stock')->where('id', $value->purchase_stock_id)->get();
                    // dd($remoteData_purchase_stock);


                    foreach ($remoteData_purchase_stock as $purchase_stock_value) {
                        $get_purchase_stock_value = DB::table('purchase_stock')
                            ->where('id', $purchase_stock_value->id)
                            ->first();

                        // Determine the local purchase_stock id to use for purchase_stock_entry
                        if ($get_purchase_stock_value) {
                            // Record already exists locally â€” capture its local id
                            $localPurchaseStockId = $get_purchase_stock_value->id;
                            DB::table('purchase_stock')->where('id', $localPurchaseStockId)->update([
                                'sku_date'             => $purchase_stock_value->sku_date,
                                'sku_id'               => $purchase_stock_value->sku_id,
                                'supplier_id'          => $purchase_stock_value->supplier_id,
                                'purchase_bill_number' => $purchase_stock_value->purchase_bill_number,
                                'total'                => $purchase_stock_value->total,
                            ]);
                        } else {
                            // New record â€” insert and capture the new local id
                            $localPurchaseStockId = DB::connection('mysql')->table('purchase_stock')->insertGetId([
                                'sku_date'             => $purchase_stock_value->sku_date,
                                'sku_id'               => $purchase_stock_value->sku_id,
                                'supplier_id'          => $purchase_stock_value->supplier_id,
                                'purchase_bill_number' => $purchase_stock_value->purchase_bill_number,
                                'total'                => $purchase_stock_value->total,
                            ]);
                        }

                        // Sync purchase_stock_entry for this purchase_stock
                        $remoteData_purchase_stock_entry = DB::connection('remote_mysql')
                            ->table('purchase_stock_entry')
                            ->where('purchase_stock_id', $value->purchase_stock_id)
                            ->get();

                        foreach ($remoteData_purchase_stock_entry as $purchase_stock_entry_value) {
                            $get_purchase_stock_entry = DB::table('purchase_stock_entry')
                                ->where('id', $purchase_stock_entry_value->id)
                                ->first();

                            if ($get_purchase_stock_entry) {
                                DB::table('purchase_stock_entry')->where('id', $get_purchase_stock_entry->id)->update([
                                    'purchase_stock_id' => $purchase_stock_entry_value->purchase_stock_id,
                                    'brand_id'          => $purchase_stock_entry_value->brand_id,
                                    'category_id'       => $purchase_stock_entry_value->category_id,
                                    'sub_category_id'   => $purchase_stock_entry_value->sub_category_id,
                                    'product_id'        => $purchase_stock_entry_value->product_id,
                                    'pack_id'           => $purchase_stock_entry_value->pack_id,
                                    'price_id'          => $purchase_stock_entry_value->price_id,
                                    'qty'               => $purchase_stock_entry_value->qty,
                                    'exp_date'          => $purchase_stock_entry_value->exp_date,
                                ]);
                            } else {
                                // Use the local purchase_stock id (not undefined $fff)
                                DB::table('purchase_stock_entry')->insert([
                                    'purchase_stock_id' => $localPurchaseStockId,
                                    'brand_id'          => $purchase_stock_entry_value->brand_id,
                                    'category_id'       => $purchase_stock_entry_value->category_id,
                                    'sub_category_id'   => $purchase_stock_entry_value->sub_category_id,
                                    'product_id'        => $purchase_stock_entry_value->product_id,
                                    'pack_id'           => $purchase_stock_entry_value->pack_id,
                                    'price_id'          => $purchase_stock_entry_value->price_id,
                                    'qty'               => $purchase_stock_entry_value->qty,
                                    'exp_date'          => $purchase_stock_entry_value->exp_date,
                                ]);
                            }
                        }

                    }
                }
                if ($getStoreAssign) {
                    DB::table('store_assign')->where('id', $getStoreAssign->id)->update([
                        // 'store_id' => $value->store_id,
                        'assign_bill_number' => $value->assign_bill_number,
                        'total' => $value->total
                    ]);
                } else {
                    DB::table('store_assign')->insert([
                        'id' => ++$currentStoreAssignId,
                        'store_id' => $value->store_id,
                        'assign_bill_number' => $value->assign_bill_number,
                        'total' => $value->total
                    ]);
                }

                $localStoreAssignId = $getStoreAssign ? $getStoreAssign->id : $currentStoreAssignId;

                $remoteDatapurchase_request = DB::connection('remote_mysql')->table('purchase_request')->where('store_assign_id', $value->id)->get();
                foreach ($remoteDatapurchase_request as $reqValue) {
                    $getPurchaseRequest = DB::table('purchase_request')->where([
                        'store_assign_id' => $localStoreAssignId,
                        'brand_id' => $reqValue->brand_id,
                        'product_id' => $reqValue->product_id,
                        'pack_id' => $reqValue->pack_id,
                    ])->first();

                    if ($getPurchaseRequest) {
                        DB::table('purchase_request')->where('id', $getPurchaseRequest->id)->update([
                            'brand_id' => $reqValue->brand_id,
                            'product_id' => $reqValue->product_id,
                            'pack_id' => $reqValue->pack_id,
                            'price_id' => $reqValue->price_id,
                            'qty' => $reqValue->qty,
                            'qty_left' => $reqValue->qty_left,
                            'exp_date' => $reqValue->exp_date,
                        ]);
                    } else {
                        DB::table('purchase_request')->insert([
                            'id' => ++$currentPurchaseRequestId,
                            'store_assign_id' => $localStoreAssignId,
                            'brand_id' => $reqValue->brand_id,
                            'product_id' => $reqValue->product_id,
                            'pack_id' => $reqValue->pack_id,
                            'price_id' => $reqValue->price_id,
                            'qty' => $reqValue->qty,
                            'qty_left' => $reqValue->qty_left,
                            'exp_date' => $reqValue->exp_date,
                        ]);
                    }
                }


            }




            $remoteDataCustomer = DB::connection('remote_mysql')->table('customer')->get();
            foreach ($remoteDataCustomer as $value) {

                if(Customer::where('id', $value->id)->doesntExist()){
                    DB::table('customer')->insert([
                        'id' => $value->id,
                        'name' => $value->name,
                        'mail' => $value->mail,
                        'phone' => $value->phone,
                        'status' => $value->status
                    ]);
                }

            }

            $remoteDataDoctor = DB::connection('remote_mysql')->table('doctor')->get();
            foreach ($remoteDataDoctor as $value) {

                if(Doctor::where('id', $value->id)->doesntExist()){
                    DB::table('doctor')->insert([
                        'id' => $value->id,
                        'name' => $value->name,
                        'mail' => $value->mail,
                        'phone' => $value->phone,
                        'degree' => $value->degree,
                        'status' => $value->status
                    ]);
                }
            }

            // $remoteDataStore = DB::connection('remote_mysql')->table('store')->get();
            // foreach ($remoteDataStore as $value) {
            //     $getStore = DB::table('store')->where([
            //         'name' => $value->name,
            //     ])->first();
            //     if ($getStore) {
            //         if ($value->store_meta_id == $storeId) {
            //             DB::table('store')->where('store_meta_id', $value->store_meta_id)->update([
            //                 // 'id' => ++$currentDoctorId,
            //                 'name' => $value->name,
            //                 'store_address' => $value->store_address,
            //                 'dl_number' => $value->dl_number,
            //                 'helpline_number' => $value->helpline_number,
            //                 'store_mail' => $value->store_mail,
            //                 'store_start_date' => $value->store_start_date,
            //                 'store_meta_id' => $value->store_meta_id,
            //                 'store_pass_key' => $value->store_pass_key,
            //                 'store_status' => $value->store_status,
            //                 'store_verify_status' => 0
            //             ]);
            //         } else {

            //             DB::table('store')->where('id', $getStore->id)->update([
            //                 'name' => $value->name,
            //                 'store_address' => '',
            //                 'dl_number' => '',
            //                 'helpline_number' => '',
            //                 'store_mail' => '',
            //                 'store_start_date' => '',
            //                 'store_meta_id' => '',
            //                 'store_pass_key' => '',
            //                 'store_status' => '',
            //                 'store_verify_status' => 0
            //             ]);
            //         }
            //     } else {
            //         if ($value->store_meta_id == $storeId) {
            //             DB::table('store')->where('store_meta_id', $value->store_meta_id)->update([
            //                 // 'id' => ++$currentStoreId,
            //                 'name' => $value->name,
            //                 'store_address' => $value->store_address,
            //                 'dl_number' => $value->dl_number,
            //                 'helpline_number' => $value->helpline_number,
            //                 'store_mail' => $value->store_mail,
            //                 'store_start_date' => $value->store_start_date,
            //                 'store_meta_id' => $value->store_meta_id,
            //                 'store_pass_key' => $value->store_pass_key,
            //                 'store_status' => $value->store_status,
            //                 'store_verify_status' => $value->store_verify_status
            //             ]);
            //         } else {

            //             DB::table('store')->insert([
            //                 'id' => ++$currentStoreId,
            //                 'name' => $value->name,
            //                 'store_address' => '',
            //                 'dl_number' => '',
            //                 'helpline_number' => '',
            //                 'store_mail' => '',
            //                 'store_start_date' => '',
            //                 'store_meta_id' => '',
            //                 'store_pass_key' => '',
            //                 'store_status' => '',
            //                 'store_verify_status' => 0
            //             ]);
            //         }
            //     }
            // }

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

    private function syncInFromAdmin(Request $request, $storeId)
    {
        try {
            $storeMetaId = session('storeId') ?: $storeId;

            if (!$storeMetaId) {
                return response()->json([
                    'status' => 422,
                    'resStatus' => false,
                    'message' => 'Store session not found. Please login again.',
                ], 422);
            }

            $remoteStore = DB::connection('remote_mysql')
                ->table('store')
                ->where('store_meta_id', $storeMetaId)
                ->first();

            if (!$remoteStore) {
                $this->writeSyncHistory('Failed', 'Store not found in admin database', 'Sync In');

                return response()->json([
                    'status' => 404,
                    'resStatus' => false,
                    'message' => 'Store not found in admin database.',
                ], 404);
            }

            DB::beginTransaction();

            $summary = [
                'brand' => $this->syncRemoteTable('brand', ['id', 'brand_name', 'status', 'created_at', 'updated_at']),
                'category' => $this->syncRemoteTable('category', ['id', 'category_name', 'status', 'created_at', 'updated_at']),
                'sub_category' => $this->syncRemoteTable('sub_category', ['id', 'category_id', 'sub_category_name', 'status', 'created_at', 'updated_at']),
                'supplier' => $this->syncRemoteTable('supplier', ['id', 'supplier_name', 'status', 'created_at', 'updated_at']),
                'pack' => $this->syncRemoteTable('pack', ['id', 'pack_name', 'status', 'created_at', 'updated_at']),
                'price' => $this->syncRemoteTable('price', ['id', 'price_name', 'created_at', 'updated_at']),
                'product' => $this->syncRemoteTable('product', ['id', 'product_name', 'brand_id', 'category_id', 'sub_category_id', 'hsn_code', 'gst', 'status', 'created_at', 'updated_at']),
                'customer' => $this->syncCustomerTable(),
                'staff'  => $this->syncStaffTable(),
                'doctor' => $this->syncRemoteTable('doctor', ['id', 'name', 'mail', 'phone', 'degree', 'status', 'created_at', 'updated_at']),
                'inhouse_product' => $this->syncRemoteTable('inhouse_product', ['id', 'product_name', 'price', 'category_id', 'sub_category_id', 'pack_id', 'status', 'created_at', 'updated_at']),
            ];

            $summary['store_assign'] = $this->syncStoreAssignments($remoteStore, $storeMetaId);

            $localStore = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
            $summary['incoming_transfers'] = $this->applyIncomingTransfers($localStore);
            $this->syncOutgoingTransferStatuses($localStore);

            $this->writeSyncHistory('Succeed', null, 'Sync In');
            DB::commit();

            return response()->json([
                'status' => 200,
                'resStatus' => true,
                'message' => 'Sync in completed successfully.',
                'summary' => $summary,
            ], 200);
        } catch (\Throwable $th) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            $this->writeSyncHistory('Failed', $th->getMessage(), 'Sync In');

            return response()->json([
                'status' => 500,
                'resStatus' => false,
                'message' => 'Sync in failed.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    private function syncRemoteTable(string $table, array $columns): int
    {
        $query = DB::connection('remote_mysql')->table($table)->select($columns);
        $localLatestUpdate = DB::table($table)->max('updated_at');

        if ($localLatestUpdate && DB::table($table)->exists()) {
            $query->where(function ($subQuery) use ($localLatestUpdate) {
                $subQuery->where('updated_at', '>', $localLatestUpdate)
                    ->orWhereNull('updated_at');
            });
        }

        $rows = $query->get();

        foreach ($rows as $row) {
            DB::table($table)->updateOrInsert(
                ['id' => $row->id],
                $this->onlyExistingColumns($table, (array) $row)
            );
        }

        return $rows->count();
    }

    private function syncOutgoingTransferStatuses(?object $localStore): void
    {
        if (!$localStore) return;

        // Find transfers this store sent that are still pending locally
        $pendingLocal = DB::table('stock_transfer')
            ->where('from_store_id', $localStore->id)
            ->where('status', 'pending')
            ->pluck('transfer_no');

        if ($pendingLocal->isEmpty()) return;

        // Check which ones are now received on admin
        $receivedOnAdmin = DB::connection('remote_mysql')
            ->table('stock_transfer')
            ->whereIn('transfer_no', $pendingLocal)
            ->where('status', 'received')
            ->get(['transfer_no', 'received_at']);

        foreach ($receivedOnAdmin as $adminTransfer) {
            DB::table('stock_transfer')
                ->where('transfer_no', $adminTransfer->transfer_no)
                ->update([
                    'status'      => 'received',
                    'received_at' => $adminTransfer->received_at,
                    'updated_at'  => now(),
                ]);
        }
    }

    private function syncStaffTable(): int
    {
        $remoteRows = DB::connection('remote_mysql')
            ->table('staff')
            ->select(['stf_id', 'store_id', 'name', 'mail', 'phone', 'status', 'created_at', 'updated_at'])
            ->whereNotNull('stf_id')
            ->get();

        foreach ($remoteRows as $row) {
            DB::table('staff')->updateOrInsert(
                ['store_id' => $row->store_id, 'phone' => $row->phone],
                $this->onlyExistingColumns('staff', [
                    'stf_id'     => $row->stf_id,
                    'store_id'   => $row->store_id,
                    'name'       => $row->name,
                    'mail'       => $row->mail,
                    'phone'      => $row->phone,
                    'status'     => $row->status,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ])
            );
        }

        return $remoteRows->count();
    }

    private function syncCustomerTable(): int
    {
        $remoteRows = DB::connection('remote_mysql')
            ->table('customer')
            ->select(['cus_id', 'store_id', 'name', 'mail', 'phone', 'status', 'created_at', 'updated_at'])
            ->whereNotNull('cus_id')
            ->get();

        foreach ($remoteRows as $row) {
            DB::table('customer')->updateOrInsert(
                ['store_id' => $row->store_id, 'phone' => $row->phone],
                $this->onlyExistingColumns('customer', [
                    'cus_id'     => $row->cus_id,
                    'store_id'   => $row->store_id,
                    'name'       => $row->name,
                    'mail'       => $row->mail,
                    'phone'      => $row->phone,
                    'status'     => $row->status,
                    'created_at' => $row->created_at ?? now(),
                    'updated_at' => $row->updated_at ?? now(),
                ])
            );
        }

        return $remoteRows->count();
    }

    private function syncStoreAssignments(object $remoteStore, string $storeMetaId): array
    {
        $localStore = DB::table('store')->where('store_meta_id', $storeMetaId)->first();

        if (!$localStore) {
            DB::table('store')->insert($this->onlyExistingColumns('store', [
                'id' => $remoteStore->id,
                'name' => $remoteStore->name,
                'store_address' => $remoteStore->store_address,
                'dl_number' => $remoteStore->dl_number ?? null,
                'helpline_number' => $remoteStore->helpline_number ?? null,
                'store_mail' => $remoteStore->store_mail,
                'store_start_date' => $remoteStore->store_start_date,
                'store_meta_id' => $remoteStore->store_meta_id,
                'store_pass_key' => $remoteStore->store_pass_key,
                'store_status' => $remoteStore->store_status,
                'store_verify_status' => 1,
                'created_at' => $remoteStore->created_at ?? now(),
                'updated_at' => $remoteStore->updated_at ?? now(),
            ]));

            $localStore = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
        } else {
            DB::table('store')->where('id', $localStore->id)->update($this->onlyExistingColumns('store', [
                'name' => $remoteStore->name,
                'store_address' => $remoteStore->store_address,
                'dl_number' => $remoteStore->dl_number ?? null,
                'helpline_number' => $remoteStore->helpline_number ?? null,
                'store_mail' => $remoteStore->store_mail,
                'store_start_date' => $remoteStore->store_start_date,
                'store_status' => $remoteStore->store_status,
                'store_verify_status' => 1,
                'updated_at' => $remoteStore->updated_at ?? now(),
            ]));
        }

        $remoteAssignments = DB::connection('remote_mysql')
            ->table('store_assign')
            ->where('store_id', $remoteStore->id)
            ->get();

        $summary = [
            'assignments' => 0,
            'purchase_stock' => 0,
            'purchase_stock_entry' => 0,
            'purchase_request_inserted' => 0,
            'purchase_request_updated' => 0,
        ];

        foreach ($remoteAssignments as $assignment) {
            $localAssignment = DB::table('store_assign')
                ->where('assign_bill_number', $assignment->assign_bill_number)
                ->where('store_id', $localStore->id)
                ->first();

            if ($localAssignment) {
                DB::table('store_assign')->where('id', $localAssignment->id)->update($this->onlyExistingColumns('store_assign', [
                    'store_id' => $localStore->id,
                    'assign_bill_number' => $assignment->assign_bill_number,
                    'total' => $assignment->total,
                    'updated_at' => $assignment->updated_at ?? now(),
                ]));
                $localStoreAssignId = $localAssignment->id;
            } else {
                $localStoreAssignId = DB::table('store_assign')->insertGetId($this->onlyExistingColumns('store_assign', [
                    'store_id' => $localStore->id,
                    'assign_bill_number' => $assignment->assign_bill_number,
                    'total' => $assignment->total,
                    'created_at' => $assignment->created_at ?? now(),
                    'updated_at' => $assignment->updated_at ?? now(),
                ]));
            }

            $summary['assignments']++;

            if ($assignment->purchase_stock_id) {
                $summary['purchase_stock'] += $this->syncAssignedPurchaseStock($assignment->purchase_stock_id);
                $summary['purchase_stock_entry'] += $this->syncAssignedPurchaseStockEntries($assignment->purchase_stock_id);
            }

            $remoteRequests = DB::connection('remote_mysql')
                ->table('purchase_request')
                ->where('store_assign_id', $assignment->id)
                ->get();

            foreach ($remoteRequests as $remoteRequest) {
                $localRequest = DB::table('purchase_request')
                    ->where('store_assign_id', $localStoreAssignId)
                    ->where('brand_id', $remoteRequest->brand_id)
                    ->where('product_id', $remoteRequest->product_id)
                    ->where('pack_id', $remoteRequest->pack_id)
                    ->first();

                if ($localRequest) {
                    DB::table('purchase_request')->where('id', $localRequest->id)->update($this->onlyExistingColumns('purchase_request', [
                        'price_id' => $remoteRequest->price_id,
                        'qty_left' => $remoteRequest->qty_left,
                        'exp_date' => $remoteRequest->exp_date,
                        'updated_at' => $remoteRequest->updated_at ?? now(),
                    ]));
                    $summary['purchase_request_updated']++;
                    continue;
                }

                DB::table('purchase_request')->insert($this->onlyExistingColumns('purchase_request', [
                    'store_assign_id' => $localStoreAssignId,
                    'brand_id' => $remoteRequest->brand_id,
                    'product_id' => $remoteRequest->product_id,
                    'pack_id' => $remoteRequest->pack_id,
                    'price_id' => $remoteRequest->price_id,
                    'qty' => $remoteRequest->qty,
                    'qty_left' => $remoteRequest->qty_left,
                    'exp_date' => $remoteRequest->exp_date,
                    'created_at' => $remoteRequest->created_at ?? now(),
                    'updated_at' => $remoteRequest->updated_at ?? now(),
                ]));
                $summary['purchase_request_inserted']++;
            }
        }

        return $summary;
    }

    private function syncAssignedPurchaseStock(int $purchaseStockId): int
    {
        $purchaseStock = DB::connection('remote_mysql')
            ->table('purchase_stock')
            ->where('id', $purchaseStockId)
            ->first();

        if (!$purchaseStock) {
            return 0;
        }

        DB::table('purchase_stock')->updateOrInsert(
            ['id' => $purchaseStock->id],
            $this->onlyExistingColumns('purchase_stock', [
                'id' => $purchaseStock->id,
                'sku_date' => $purchaseStock->sku_date,
                'sku_id' => $purchaseStock->sku_id,
                'supplier_id' => $purchaseStock->supplier_id,
                'purchase_bill_number' => $purchaseStock->purchase_bill_number,
                'total' => $purchaseStock->total,
                'created_at' => $purchaseStock->created_at ?? now(),
                'updated_at' => $purchaseStock->updated_at ?? now(),
            ])
        );

        return 1;
    }

    private function syncAssignedPurchaseStockEntries(int $purchaseStockId): int
    {
        $entries = DB::connection('remote_mysql')
            ->table('purchase_stock_entry')
            ->where('purchase_stock_id', $purchaseStockId)
            ->get();

        foreach ($entries as $entry) {
            $localEntry = DB::table('purchase_stock_entry')->where('id', $entry->id)->first();

            $payload = [
                'id' => $entry->id,
                'purchase_stock_id' => $entry->purchase_stock_id,
                'brand_id' => $entry->brand_id,
                'category_id' => $entry->category_id,
                'sub_category_id' => $entry->sub_category_id,
                'product_id' => $entry->product_id,
                'pack_id' => $entry->pack_id,
                'price_id' => $entry->price_id,
                'exp_date' => $entry->exp_date,
                'created_at' => $entry->created_at ?? now(),
                'updated_at' => $entry->updated_at ?? now(),
            ];

            if (!$localEntry) {
                $payload['qty'] = $entry->qty;
            }

            DB::table('purchase_stock_entry')->updateOrInsert(
                ['id' => $entry->id],
                $this->onlyExistingColumns('purchase_stock_entry', $payload)
            );
        }

        return $entries->count();
    }

    private function applyIncomingTransfers(?object $localStore): int
    {
        if (!$localStore) return 0;

        // Get this store's ID on the remote DB
        $remoteStore = DB::connection('remote_mysql')
            ->table('store')
            ->where('store_meta_id', $localStore->store_meta_id)
            ->first();

        if (!$remoteStore) return 0;

        // Query pending transfers destined for this store using correct remote schema
        $pendingTransfers = DB::connection('remote_mysql')
            ->table('stock_transfer')
            ->where('to_store_id', $remoteStore->id)
            ->where('status', 'pending')
            ->get();

        if ($pendingTransfers->isEmpty()) return 0;

        $applied = 0;

        foreach ($pendingTransfers as $transfer) {
            // Skip if already imported by transfer_no
            if (DB::table('stock_transfer')->where('transfer_no', $transfer->transfer_no)->exists()) continue;

            $items = DB::connection('remote_mysql')
                ->table('stock_transfer_items')
                ->where('transfer_id', $transfer->id)
                ->get();

            // Insert local transfer header
            DB::table('stock_transfer')->insert($this->onlyExistingColumns('stock_transfer', [
                'transfer_no'   => $transfer->transfer_no,
                'from_store_id' => $transfer->from_store_id,
                'to_store_id'   => $localStore->id,
                'transfer_date' => $transfer->transfer_date,
                'status'        => 'received',
                'received_at'   => now(),
                'notes'         => $transfer->notes,
                'created_at'    => $transfer->created_at ?? now(),
                'updated_at'    => now(),
            ]));

            $localTransfer = DB::table('stock_transfer')->where('transfer_no', $transfer->transfer_no)->first();

            foreach ($items as $item) {
                DB::table('stock_transfer_items')->insert($this->onlyExistingColumns('stock_transfer_items', [
                    'transfer_id'         => $localTransfer->id,
                    'purchase_request_id' => $item->purchase_request_id ?? null,
                    'product_id'          => $item->product_id,
                    'product_name'        => $item->product_name,
                    'pack_id'             => $item->pack_id,
                    'pack_name'           => $item->pack_name,
                    'price_id'            => $item->price_id,
                    'brand_id'            => $item->brand_id ?? null,
                    'unit_value'          => $item->unit_value,
                    'qty'                 => $item->qty,
                    'created_at'          => $item->created_at ?? now(),
                    'updated_at'          => now(),
                ]));

                // Update local stock
                $existingPR = DB::table('purchase_request')
                    ->where('product_id', $item->product_id)
                    ->where('pack_id', $item->pack_id)
                    ->where('price_id', $item->price_id)
                    ->first();

                if ($existingPR) {
                    DB::table('purchase_request')->where('id', $existingPR->id)->update([
                        'qty'        => (string)((float)$existingPR->qty + (float)$item->qty),
                        'qty_left'   => (string)((float)($existingPR->qty_left ?? $existingPR->qty) + (float)$item->qty),
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('purchase_request')->insert($this->onlyExistingColumns('purchase_request', [
                        'store_assign_id' => null,
                        'transfer_id'     => $localTransfer->id,
                        'brand_id'        => $item->brand_id ?? 1,
                        'product_id'      => $item->product_id,
                        'pack_id'         => $item->pack_id,
                        'price_id'        => $item->price_id,
                        'qty'             => $item->qty,
                        'qty_left'        => $item->qty,
                        'exp_date'        => date('Y-m-d', strtotime('+1 year')),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]));
                }
            }

            // Mark as received on admin
            DB::connection('remote_mysql')->table('stock_transfer')
                ->where('id', $transfer->id)
                ->update(['status' => 'received', 'received_at' => now(), 'updated_at' => now()]);

            $applied++;
        }

        return $applied;
    }

    private function onlyExistingColumns(string $table, array $payload): array
    {
        $columns = Schema::getColumnListing($table);

        return array_intersect_key($payload, array_flip($columns));
    }

    private function writeSyncHistory(string $status, ?string $message = null, string $type = ''): void
    {
        $payload = [
            'sync_date'   => date('Y-m-d'),
            'sync_type'   => $type,
            'sync_status' => $message ? "{$status}: " . substr($message, 0, 180) : $status,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];

        DB::table('sync_history')->insert($this->onlyExistingColumns('sync_history', $payload));
    }

    public function syncAllTables()
    {
        $tables = $this->getAllTables();

        foreach ($tables as $table) {
            // Sync logic for each table
        }

        return response()->json(['message' => 'Database synced successfully']);
    }

    private function getAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . env('DB_DATABASE');

        return collect($tables)->pluck($tableKey)->toArray();
    }

    private function pushStaffToAdmin($adminDB): int
    {
        if (!$adminDB->getSchemaBuilder()->hasTable('staff')) return 0;

        $adminColumns = $adminDB->getSchemaBuilder()->getColumnListing('staff');
        $localStaff   = DB::table('staff')->whereNotNull('stf_id')->get();

        foreach ($localStaff as $staff) {
            $payload = array_intersect_key((array) $staff, array_flip($adminColumns));
            unset($payload['id']); // never overwrite admin's PK
            $adminDB->table('staff')->updateOrInsert(
                ['store_id' => $staff->store_id, 'phone' => $staff->phone],
                $payload
            );
        }

        return $localStaff->count();
    }

    private function pushCustomersToAdmin($adminDB): int
    {
        if (!$adminDB->getSchemaBuilder()->hasTable('customer')) return 0;

        $adminColumns = $adminDB->getSchemaBuilder()->getColumnListing('customer');
        $localCustomers = DB::table('customer')->whereNotNull('cus_id')->get();

        foreach ($localCustomers as $customer) {
            $payload = array_intersect_key((array) $customer, array_flip($adminColumns));
            unset($payload['id']); // never overwrite admin's PK
            $adminDB->table('customer')->updateOrInsert(
                ['store_id' => $customer->store_id, 'phone' => $customer->phone],
                $payload
            );
        }

        return $localCustomers->count();
    }

    public function sendDataToAdminDatabase(Request $request, $storeId)
    {
        DB::beginTransaction();
        try {
            $adminDB = DB::connection('remote_mysql');
            $adminDB->statement('SET FOREIGN_KEY_CHECKS=0');

            // Push customers and staff first - billing references them on admin.
            $summary['customer'] = $this->pushCustomersToAdmin($adminDB);
            $summary['staff']    = $this->pushStaffToAdmin($adminDB);

            // Only billing tables are sent to admin - everything else is admin's source of truth.
            // Parent must come before child to satisfy foreign key constraints on upsert.
            $billingTables = [
                'customer_billing',
                'customer_product_billing',
                'staff_billing',
                'staff_product_billing',
                'stock_transfer',
                'stock_transfer_items',
            ];

            $summary = [];

            foreach ($billingTables as $table) {
                if (!Schema::hasTable($table)) continue;
                if (!$adminDB->getSchemaBuilder()->hasTable($table)) continue;

                $adminColumns = $adminDB->getSchemaBuilder()->getColumnListing($table);
                $lastSyncedAt = $adminDB->table($table)->max('updated_at') ?? '1970-01-01 00:00:00';
                $adminTransferNos = $adminDB->table('stock_transfer')->pluck('transfer_no')->toArray();
                $adminIds    = $adminDB->table($table)->pluck('id')->toArray();
                $updatedRows = DB::table($table)->where('updated_at', '>', $lastSyncedAt)->get();
                $missingRows = DB::table($table)->whereNotIn('id', $adminIds)->get();
                $newRows     = $updatedRows->merge($missingRows)->unique('id');

                if ($newRows->isEmpty()) {
                    $summary[$table] = 0;
                    continue;
                }

                if ($table === 'stock_transfer') {
                    // Use transfer_no as the business key — local id and admin id diverge.
                    foreach ($newRows as $row) {
                        $data = array_intersect_key((array) $row, array_flip($adminColumns));
                        unset($data['id']);
                        $adminDB->table('stock_transfer')->updateOrInsert(
                            ['transfer_no' => $row->transfer_no],
                            $data
                        );
                    }
                    $summary[$table] = $newRows->count();

                } elseif ($table === 'stock_transfer_items') {
                    // Build local_id -> admin_id map via transfer_no
                    $localTransfers = DB::table('stock_transfer')->get()->keyBy('id');
                    $transferIdMap  = [];
                    foreach ($localTransfers as $localId => $lt) {
                        $adminTransfer = $adminDB->table('stock_transfer')
                            ->where('transfer_no', $lt->transfer_no)
                            ->first();
                        if ($adminTransfer) {
                            $transferIdMap[$localId] = $adminTransfer->id;
                        }
                    }

                    $pushed = 0;
                    foreach ($newRows as $item) {
                        $adminTransferId = $transferIdMap[$item->transfer_id] ?? null;
                        if (!$adminTransferId) continue;

                        $data = array_intersect_key((array) $item, array_flip($adminColumns));
                        unset($data['id']);
                        $data['transfer_id'] = $adminTransferId;

                        $adminDB->table('stock_transfer_items')->updateOrInsert(
                            ['transfer_id' => $adminTransferId, 'product_id' => $item->product_id, 'pack_id' => $item->pack_id],
                            $data
                        );
                        $pushed++;
                    }
                    $summary[$table] = $pushed;

                } else {
                    $dataArray = $newRows->map(function ($row) use ($adminColumns) {
                        return array_intersect_key((array) $row, array_flip($adminColumns));
                    })->toArray();

                    $updateColumns = array_values(array_filter(array_keys($dataArray[0]), function ($k) { return $k !== 'id'; }));
                    $adminDB->table($table)->upsert($dataArray, ['id'], $updateColumns);
                    $summary[$table] = count($dataArray);
                }

                \Log::info('Sync out: pushed ' . $table . ' - ' . $summary[$table] . ' record(s).');
            }

            $adminDB->statement('SET FOREIGN_KEY_CHECKS=1');
            $this->writeSyncHistory('Succeed', null, 'Sync Out');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sync out completed successfully.',
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            try { DB::connection('remote_mysql')->statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $ignored) {}
            DB::rollBack();
            $this->writeSyncHistory('Failed', $e->getMessage(), 'Sync Out');
            \Log::error('Sync out failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Sync out failed: ' . $e->getMessage()]);
        }
    }



    public function insertStore(Request $request)
    {
        try {
            $payload = [
                "name" => $request->name,
                "store_address" => $request->store_address,
                "dl_number" => $request->dl_number,
                "helpline_number" => $request->helpline_number,
                "store_mail" => $request->store_mail,
                "store_start_date" => $request->store_start_date,
                "store_meta_id" => $request->store_meta_id,
                "store_pass_key" => $request->store_pass_key,
                "store_status" => $request->store_status,
                "store_verify_status" => 1
            ];

            $insertStore = DB::table('store')->insert($payload);
            if ($insertStore) {
                return response()->json([
                    'status' => 200,
                    'message' => "Store insert.",
                    'resStatus' => true,
                ], 200);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function checkStore()
    {
        try {
            $getStore = DB::table('store')->get();

            if (count($getStore) > 0) {
                return response()->json([
                    'status' => 200,
                    'resStatus' => true,
                    'data' => $getStore
                ], 200);
            } else {
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

    public function getSyncHist(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $search = $request->query('search');
            $page = $request->query('page');
            $limit = $request->query('limit');
            $query = DB::table('sync_history')->orderByDesc('id');
            if ($startDate) {
                $query->where('sync_date', '>=', $startDate);
            }
            if ($endDate) {
                $query->where('sync_date', '<=', $endDate);
            }
            if ($page && $limit) {
                $history = $query->paginate($limit, ['*'], 'page', $page ?? 1);
            } else {
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

    public function backupSQL()
    {
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $port = env('DB_PORT');

        $fileName = "backup-" . date('Y-m-d_H-i-s') . ".sql";
        $relativeFilePath = "backup" . DIRECTORY_SEPARATOR . $fileName;
        $filePath = storage_path("app" . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . $relativeFilePath);

        if (!file_exists(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'backup'))) {
            mkdir(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'backup'), 0777, true);
        }

        // Provide the full path to mysqldump for XAMPP
        $mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump.exe"; // Update this path as needed

        // Ensure correct escaping of arguments and paths
        $command = "$mysqldumpPath --user=" . escapeshellarg(trim($username)) . " --password=" . escapeshellarg(trim($password)) . " --host=" . escapeshellarg(trim($host)) . " --port=" . intval(trim($port)) . " " . escapeshellarg(trim($database)) . " > \"" . $filePath . "\"";

        // Debug command before executing
        // dd($command);

        // Use proc_open for better control over the command execution
        $descriptorSpec = [
            0 => ["pipe", "r"],  // STDIN
            1 => ["pipe", "w"],  // STDOUT
            2 => ["pipe", "w"],  // STDERR
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (!is_resource($process)) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create backup.'], 500);
        }

        // Close the pipes to avoid deadlock
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $result = proc_close($process);

        if ($result !== 0) {
            return response()->json(['status' => 'error', 'message' => 'Failed to create backup.', 'error' => $errorOutput], 500);
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

    public function getBackup()
    {
        $backupFile = DB::table('backup')->get();
        return response()->json(['status' => 'success', 'data' => $backupFile]);

    }

    public function purchase_request_all(Request $request)
    {
        try {
            $productId = $request->query('product_id') ?: $request->query('id');
            $packId = $request->query('pack_id');

            $query = DB::table('purchase_request');

            if ($productId) {
                $query->where('product_id', $productId);
            }
            if ($packId) {
                $query->where('pack_id', $packId);
            }

            $purchase_request = $query->get();

            return response()->json(['status' => 'success', 'purchase_request' => $purchase_request]);

        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function updateProductQty(Request $request)
    {
        $product = Product::find($request->product_id);
        if ($product) {
            $product->quantity = $product->quantity - $request->assigned_qty;
            $product->save();
            return response()->json(['success' => true, 'message' => 'Product quantity updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }
    }
    public function verifyStore(Request $request){
        try {
            $store_meta_id = $request->get('storeId');
            $store_passkey = $request->get('storePassKey');
            $store_mail    = $request->get('storeMail');

            $remoteDataStore = DB::connection('remote_mysql')->table('store')->where('store_meta_id', $store_meta_id)->first();

            if (!$remoteDataStore) {
                return response()->json(['success' => false, 'message' => 'Store not found.'], 404);
            }

            if ($store_passkey !== $remoteDataStore->store_pass_key || $store_mail !== $remoteDataStore->store_mail) {
                return response()->json(['success' => false, 'message' => 'Invalid Store ID, Pass Key or Email.'], 401);
            }

            // Check if already set up
            $existing = DB::table('store')->where('store_meta_id', $store_meta_id)->exists();
            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Store is already verified and set up.'], 409);
            }

            Store::create([
                "id"                 => $remoteDataStore->id,
                "name"               => $remoteDataStore->name,
                "store_address"      => $remoteDataStore->store_address,
                "dl_number"          => $remoteDataStore->dl_number,
                "helpline_number"    => $remoteDataStore->helpline_number,
                "store_mail"         => $remoteDataStore->store_mail,
                "store_start_date"   => $remoteDataStore->store_start_date,
                "store_meta_id"      => $remoteDataStore->store_meta_id,
                "store_pass_key"     => $remoteDataStore->store_pass_key,
                "store_status"       => $remoteDataStore->store_status,
                "store_verify_status"=> 1,
            ]);

            return response()->json(['success' => true, 'message' => 'Store verified successfully.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Verification failed: ' . $th->getMessage()], 500);
        }
    }

}

