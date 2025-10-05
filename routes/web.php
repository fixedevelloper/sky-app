<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SecurityController;
use Illuminate\Support\Facades\Route;

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
    Route::match(["POST", "GET"], '/purchases/{id}/paiements', [DashboardController::class, 'paiements'])
        ->name('paiements');
    Route::match(["POST", "GET"], '/partners', [DashboardController::class, 'partners'])
        ->name('partners');

});
