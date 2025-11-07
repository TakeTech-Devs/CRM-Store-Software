<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\CustomerBilling;
use App\Http\Controllers\Api\StaffBilling;
use App\Http\Controllers\Api\DataFetchController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\StockerTransferController;
use App\Http\Controllers\Api\ReportController;



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/customer/billing/create', [CustomerBilling:: class , 'createBilling']);
Route::get('/customer/billing/list', [CustomerBilling:: class , 'listBilling']);
route::get('/customer/bill/{billId}', [CustomerBilling::class, 'getBillDetails']);
Route::post('/customer', [CustomerBilling:: class , 'create_customer']);
Route::get('/customer', [CustomerBilling:: class , 'list_customer']);
Route::get('/customer/bill/filter', [CustomerBilling::class , 'datefilter']);
Route::get('/store/info', [CustomerBilling::class , 'getStoreInfo']);


Route::post('/staff/billing/create', [StaffBilling:: class , 'createBilling']);
Route::get('/staff/billing/list', [StaffBilling:: class , 'listBilling']);
Route::post('/staff', [StaffBilling:: class , 'create_staff']);
Route::get('/staff', [StaffBilling:: class , 'list_staff']);
Route::get('staff/bill/filter/', [StaffBilling::class , 'datefilter']);
route::get('/staff/bill/{billId}', [StaffBilling::class, 'getBillDetails']);

Route::get('/get/sync/history', [DataFetchController:: class , 'getSyncHist']);
Route::get('/store/backup', [DataFetchController:: class , 'backupSQL']);
Route::get('/backups', [DataFetchController:: class , 'getBackup']);
Route::get('/backup/{id}', [DataFetchController::class, 'deleteBackup']);

Route::get('/purchase_request', [DataFetchController::class, 'purchase_request_all']);
Route::get('/packs/{productId}', [DataController::class, 'packs_by_product']);
Route::get('store-transfer', function () {
    return response()->json(['message' => 'API is working']);
});

Route::get('/doctor', [ReportController::class, 'getDoctors']);
Route::get('/doctor-report', [ReportController::class, 'doctorWiseReport'])->name('doctor.report');
Route::get('/commulative-report', [ReportController::class, 'getCumulativeSalesReport'])->name('commulative.report');
Route::get('/gst-report', [ReportController::class, 'gstReport'])->name('gst.report');
Route::get('/expiry-report', [ReportController::class, 'expiryReport'])->name('expiry.report');
Route::get('/stock-report', [ReportController::class, 'stockReport'])->name('stock.report');
Route::get('/yesterday-sale', [ReportController::class, 'yesterdaySale']);
Route::get('/today-sale', [ReportController::class, 'todaySale']);
Route::get('/zero-stock-medicine', [ReportController::class, 'zeroStockMedicine']);
Route::get('/monthly-earnings', [ReportController::class, 'monthlyEarnings']);
