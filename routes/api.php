<?php

use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\MomoController;
use App\Http\Controllers\api\PaiementController;
use App\Http\Controllers\api\PartnerController;
use App\Http\Controllers\api\PmesController;
use App\Http\Controllers\api\PointSaleController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\PurchaseController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\SecurityApiController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::post('/login', [SecurityApiController::class, 'login']);
Route::post('/register', [SecurityApiController::class, 'register']);

Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('point-sales', PointSaleController::class);
Route::apiResource('pmes', PmesController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('partners', PartnerController::class);
Route::post('/customers/current', [CustomerController::class, 'getCurrentCustomer']);
//Route::apiResource('purchases', PurchaseController::class)->middleware('auth:sanctum');
Route::post('/purchases/customer', [PurchaseController::class, 'storeCustomer'])->middleware('auth:sanctum');
Route::post('/purchases/commercial', [PurchaseController::class, 'storeCommercial'])->middleware('auth:sanctum');
Route::get('/purchases/current', [PurchaseController::class, 'showCurrent'])->middleware('auth:sanctum');
Route::get('/purchases', [PurchaseController::class, 'index'])->middleware('auth:sanctum');
Route::apiResource('paiements', PaiementController::class);
Route::post('/momo/pay', [MomoController::class, 'pay']);
Route::get('/momo/status/{referenceId}', [MomoController::class, 'checkStatus']);
Route::get('/momo/status-sale-point/{referenceId}', [MomoController::class, 'checkStatusSalePoint']);
Route::post('/momo/token', [MomoController::class, 'getToken']);

