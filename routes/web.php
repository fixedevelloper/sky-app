<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MomoCallbackController;
use App\Http\Controllers\SecurityController;
use Illuminate\Support\Facades\Route;


Route::post('/momo/callback', [MomoCallbackController::class, 'callback'])->name('momo.callback');
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
Route::match(["POST", "GET"], '/', [SecurityController::class, 'signin'])
    ->name('login');
Route::match(["POST", "GET"], '/signout', [SecurityController::class, 'destroy'])
    ->name('signout');

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::match(["POST", "GET"], '/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::match(["POST", "GET"], '/vendors', [DashboardController::class, 'vendors'])
        ->name('vendors');
    Route::match(["POST", "GET"], '/vendors/{id}/point_sale', [DashboardController::class, 'pointSale'])
        ->name('point_sale');
    Route::match(["POST", "GET"], '/purchases', [DashboardController::class, 'purchase'])
        ->name('purchases');
    Route::match(["POST", "GET"], '/facture', [DashboardController::class, 'facture'])
        ->name('facture');
    Route::match(["POST", "GET"], '/purchase_commercial', [DashboardController::class, 'purchase_commercial'])
        ->name('purchase_commercial');
    Route::match(["POST", "GET"], '/purchases/{id}/paiements', [DashboardController::class, 'paiements'])
        ->name('paiements');
    Route::match(["POST", "GET"], '/partners', [DashboardController::class, 'partners'])
        ->name('partners');
    Route::match(["POST", "GET"], '/categories', [DashboardController::class, 'categories'])
        ->name('categories');
    Route::match(["POST", "GET"], '/products', [DashboardController::class, 'products'])
        ->name('products');
    Route::get('/momo-callbacks', function () {
        return view('admin.momo_callbacks', [
            'callbacks' => \App\Models\MomoCallback::latest()->paginate(50),
        ]);
    });

});
