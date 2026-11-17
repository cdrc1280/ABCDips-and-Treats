<?php

use App\Domains\Sales\Controllers\CouponController;
use App\Domains\Sales\Controllers\CustomOrderController;
use App\Domains\Sales\Controllers\DeliveryPoolController;
use App\Domains\Sales\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('coupons')->name('coupons.')->group(function () {
    Route::get('/', [CouponController::class, 'index'])->name('index');
    Route::get('create', [CouponController::class, 'create'])->name('create');
    Route::post('/', [CouponController::class, 'store'])->name('store');
    Route::get('{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
    Route::put('{coupon}', [CouponController::class, 'update'])->name('update');
    Route::patch('{coupon}/toggle', [CouponController::class, 'toggle'])->name('toggle');
    Route::delete('{coupon}', [CouponController::class, 'destroy'])->name('destroy');
});

Route::prefix('custom-orders')->name('custom-orders.')->group(function () {
    Route::get('/', [CustomOrderController::class, 'index'])->name('index');
    Route::get('create', [CustomOrderController::class, 'create'])->name('create');
    Route::post('/', [CustomOrderController::class, 'store'])->name('store');
    Route::get('{customOrder}/edit', [CustomOrderController::class, 'edit'])->name('edit');
    Route::put('{customOrder}', [CustomOrderController::class, 'update'])->name('update');
    Route::patch('{customOrder}/start-production', [CustomOrderController::class, 'startProduction'])->name('start-production');
    Route::patch('{customOrder}/complete', [CustomOrderController::class, 'complete'])->name('complete');
    Route::delete('{customOrder}', [CustomOrderController::class, 'destroy'])->name('destroy');
});

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('{order}', [OrderController::class, 'update'])->name('update');
    Route::patch('{order}/change-status', [OrderController::class, 'changeStatus'])->name('change-status');
});

Route::prefix('delivery-pools')->name('delivery-pools.')->group(function () {
    Route::get('/', [DeliveryPoolController::class, 'index'])->name('index');
    Route::get('create', [DeliveryPoolController::class, 'create'])->name('create');
    Route::post('/', [DeliveryPoolController::class, 'store'])->name('store');
    Route::get('{deliveryPool}/edit', [DeliveryPoolController::class, 'edit'])->name('edit');
    Route::put('{deliveryPool}', [DeliveryPoolController::class, 'update'])->name('update');
    Route::patch('{deliveryPool}/settle', [DeliveryPoolController::class, 'settle'])->name('settle');
    Route::delete('{deliveryPool}', [DeliveryPoolController::class, 'destroy'])->name('destroy');
});
