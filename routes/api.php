<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\CustomerBilling;
use App\Http\Controllers\Api\StaffBilling;
use App\Http\Controllers\Api\DataFetchController;
use App\Http\Controllers\Api\DoctorController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/customer/billing/create', [CustomerBilling:: class , 'createBilling']);
Route::get('/customer/billing/list', [CustomerBilling:: class , 'listBilling']);
Route::post('/customer', [CustomerBilling:: class , 'create_customer']);
Route::get('/customer', [CustomerBilling:: class , 'list_customer']);
Route::get('customer/bill/filter/', [CustomerBilling::class , 'datefilter']);


Route::post('/staff/billing/create', [StaffBilling:: class , 'createBilling']);
Route::get('/staff/billing/list', [StaffBilling:: class , 'listBilling']);
Route::post('/staff', [StaffBilling:: class , 'create_staff']);
Route::get('/staff', [StaffBilling:: class , 'list_staff']);
Route::get('staff/bill/filter/', [StaffBilling::class , 'datefilter']);

Route::get('/get/sync/history', [DataFetchController:: class , 'getSyncHist']);
Route::get('/store/backup', [DataFetchController:: class , 'backupSQL']);
Route::get('/backups', [DataFetchController:: class , 'getBackup']);
Route::get('/backup/{id}', [DataFetchController::class, 'deleteBackup']);

Route::post('doctor', [DoctorController::class, 'create']);
