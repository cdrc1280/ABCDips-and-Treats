<?php

use App\Domains\Storefront\Controllers\AccountController;
use App\Domains\Storefront\Controllers\OrderTrackingController;
use App\Domains\Storefront\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('account', fn () => redirect('/account/orders'))->name('account');
    Route::get('account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('account/orders/{order}', [AccountController::class, 'order'])->whereUuid('order')->name('account.orders.show');
    Route::post('account/orders/{order}/cancel', [AccountController::class, 'cancel'])->whereUuid('order')->middleware('throttle:10,1')->name('account.orders.cancel');
    Route::get('account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('account/avatar', [AccountController::class, 'updateAvatar'])->middleware('throttle:10,1')->name('account.avatar');
    Route::get('account/wishlist', [WishlistController::class, 'index'])->name('account.wishlist');
    Route::post('wishlist/{product}', [WishlistController::class, 'toggle'])->whereUuid('product')->name('wishlist.toggle');
});

Route::get('track', [OrderTrackingController::class, 'form'])->name('orders.track.form');
Route::post('track', [OrderTrackingController::class, 'lookup'])->middleware('throttle:10,1')->name('orders.track.lookup');
Route::get('track/{token}', [OrderTrackingController::class, 'show'])->middleware('throttle:60,1')->name('orders.track');
Route::get('orders/{token}/confirmation', [OrderTrackingController::class, 'confirmation'])->middleware('throttle:60,1')->name('orders.confirmation');
