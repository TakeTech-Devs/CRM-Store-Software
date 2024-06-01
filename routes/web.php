<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataFetchController;

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
    Route::get('/staff/billing', function () {
        return view('store/billing/staff/billing');
    });
    Route::get('/staff/create/billing', function () {
        return view('store/billing/staff/create');
    });
});

// Route::prefix('store')->group(function () {
Route::get('/sync-data/{storeId}', [DataFetchController::class, 'dataFetch']);
Route::post('/store', [DataFetchController::class, 'insertStore']);



// });



