<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataFetchController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\CustomerBilling;
use App\Http\Controllers\Api\StaffBilling;


use App\Http\Controllers\Api\LoginController;



Route::get('/', function (){
    return view('verify');
})->name('verify-page');

Route::get('/login-page', function (){
    return view('store.login.login');
})->name('login-page');

Route::post('/login', [LoginController::class, 'login']);


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
        Route::get('/commulative/report', function () {
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
Route::get('/purchase/request', [DataController:: class , 'purchase_bill']);
Route::get('/category', [DataController:: class , 'category_data']);
Route::get('/sub-category', [DataController:: class , 'sub_category_data']);
Route::get('/pack', [DataController:: class , 'pack_data']);
Route::get('/price', [DataController:: class , 'price_data']);
Route::post('/customer/billing/create', [CustomerBilling:: class , 'createBilling']);
Route::post('/customer/billing/update/{billId}', [CustomerBilling::class, 'updateBilling']);
Route::post('/staff/billing/create', [StaffBilling::class , 'createBilling']);












// });



