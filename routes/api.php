<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\CustomerBilling;
use App\Http\Controllers\Api\DataFetchController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/customer/billing/create', [CustomerBilling:: class , 'createBilling']);
Route::get('/customer/billing/list', [CustomerBilling:: class , 'listBilling']);


Route::get('/get/sync/history', [DataFetchController:: class , 'getSyncHist']);

