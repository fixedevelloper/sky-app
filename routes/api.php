<?php

use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\MomoController;
use App\Http\Controllers\api\PaiementController;
use App\Http\Controllers\api\PointSaleController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\PurchaseController;
use App\Http\Controllers\api\UserController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;



Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('point-sales', PointSaleController::class);
Route::apiResource('customers', CustomerController::class);
Route::post('/customers/current', [CustomerController::class, 'getCurrentCustomer']);
Route::apiResource('purchases', PurchaseController::class);
Route::apiResource('paiements', PaiementController::class);
Route::post('/momo/pay', [MomoController::class, 'pay']);
Route::get('/momo/status/{referenceId}', [MomoController::class, 'checkStatus']);
Route::post('/momo/token', [MomoController::class, 'getToken']);

