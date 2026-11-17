<?php

use App\Domains\Storefront\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
    Route::get('/', 'show')->name('show');
    Route::post('items', 'store')->middleware('throttle:60,1')->name('items.store');
    Route::patch('items/{item}', 'update')->whereUuid('item')->name('items.update');
    Route::delete('items/{item}', 'destroy')->whereUuid('item')->name('items.destroy');
    Route::post('items/{item}/restore', 'restore')->whereUuid('item')->name('items.restore');
    Route::post('batch', 'batch')->name('batch');
    Route::post('coupon', 'applyCoupon')->middleware('throttle:20,1')->name('coupon.apply');
    Route::delete('coupon', 'removeCoupon')->name('coupon.remove');
});
