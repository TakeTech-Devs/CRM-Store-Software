<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataFetchController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\CustomerBilling;
use App\Http\Controllers\Api\StaffBilling;


use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\ReportController;



Route::get('/', function (){
    return view('verify');
})->name('verify-page');

Route::get('/login-page', function (){
    return view('store.login.login');
})->name('login-page');

Route::post('/login', [LoginController::class, 'login'])->name('login');


Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout']);

    Route::prefix('store')->group(function () {
        Route::get('/dashboard', function () {
            return view('store/dashboard/home');
        });
    
        Route::get('/customer/billing', function () {
            return view('store/billing/customer/billing');
        });
        Route::get('/customer/create/billing', function () {
            return view('store/billing/customer/create');
        });
        Route::get('/customer/billing/{id}/edit', function ($id) {
            return view('store/billing/customer/edit', ['billId' => $id]);
        });
        Route::get('/staff/billing', function () {
            return view('store/billing/staff/billing');
        });
        Route::get('/staff/create/billing', function () {
            return view('store/billing/staff/create');
        });
        Route::get('/details', function () {
            return view('store/storeSync/storeDetails');
        });
        Route::get('/stock/transfer', function () {
            return view('store/stockTransfer/stockTransferList');
        });
        Route::get('/create/stockTransfer', function () {
            return view('store/stockTransfer/stockTransferCreate');
        });
        Route::get('/sync/history', function () {
            return view('store/storeSync/storeSyncHist');
        });
        Route::get('/backup', function () {
            return view('store/backup/backup');
        });
        Route::get('/report', function () {
            return view('store/reports/storeReport');
        });
        Route::get('/doctor/report', function () {
            return view('store/reports/doctorReport');
        });
        Route::get('/cumulative/report', function () {
            return view('store/reports/commulativeReport');
        });
        Route::get('/expiry/report', function () {
            return view('store/reports/expiryReport');
        });
        Route::get('/gst/report', function () {
            return view('store/reports/gstReport');
        });
        Route::get('/stock/report', function () {
            return view('store/reports/stockReport');
        });
        Route::get('/analytics', function () {
            return view('store/analytics/analytics');
        });
    });
});



// APIS
// Route::prefix('store')->group(function () {
Route::get('/sync-data/{storeId}', [DataFetchController::class, 'dataFetch']);
Route::get('/sync/out/data/{storeId}', [DataFetchController::class, 'sendDataToAdminDatabase']);
Route::post('/store', [DataFetchController::class, 'insertStore']);
Route::get('/verify/store', [DataFetchController::class, 'checkStore']);
Route::get('/customers', [DataController:: class , 'customer_data']);
Route::get('/staffs', [DataController:: class , 'staff_data']);
Route::get('/doctors', [DataController:: class , 'doctor_data']);
Route::get('/products', [DataController:: class , 'product_data']);
Route::get('/billing/product-options', [DataController::class, 'billingProductOptions']);
Route::prefix('api')->group(function () {
    Route::get('/stores', [DataController::class, 'getStores']);
    Route::get('/billing/product-options', [DataController::class, 'billingProductOptions']);
    Route::post('/stock-transfer/create', [StockTransferController::class, 'createTransfer']);
    Route::get('/stock-transfer/list', [StockTransferController::class, 'listTransfers']);
    Route::get('/stock-transfer/{id}', [StockTransferController::class, 'getTransferDetail']);
    Route::get('/analytics', [ReportController::class, 'analyticsData']);
    Route::get('/monthly-earnings', [ReportController::class, 'monthlyEarnings']);
});
Route::get('/purchase/request', [DataController:: class , 'purchase_bill']);
Route::get('/category', [DataController:: class , 'category_data']);
Route::get('/sub-category', [DataController:: class , 'sub_category_data']);
Route::get('/pack', [DataController:: class , 'pack_data']);
Route::get('/price', [DataController:: class , 'price_data']);
Route::post('/customer/billing/create', [CustomerBilling:: class , 'createBilling']);
Route::post('/customer/billing/update/{billId}', [CustomerBilling::class, 'updateBilling']);
Route::post('/staff/billing/create', [StaffBilling::class , 'createBilling']);












// });



