<?php

use App\Domains\Storefront\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
    Route::post('checkout/quote', [CheckoutController::class, 'quote'])->middleware('throttle:30,1')->name('checkout.quote');
});

// PayMongo redirects back here (the session identifies the order).
Route::get('checkout/payment-success', [CheckoutController::class, 'paymentReturn'])->middleware('throttle:30,1')->name('checkout.payment-return');
Route::get('checkout/payment-failed', [CheckoutController::class, 'paymentFailed'])->middleware('throttle:30,1')->name('checkout.payment-failed');
Route::post('orders/{token}/pay', [CheckoutController::class, 'pay'])->middleware('throttle:10,1')->name('orders.pay');
