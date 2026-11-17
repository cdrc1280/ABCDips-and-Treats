<?php

use App\Domains\Purchasing\Controllers\PurchaseOrderController;
use App\Domains\Purchasing\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [QuotationController::class, 'index'])->name('index');
    Route::get('create', [QuotationController::class, 'create'])->name('create');
    Route::post('/', [QuotationController::class, 'store'])->name('store');
    Route::get('{quotation}/edit', [QuotationController::class, 'edit'])->name('edit');
    Route::put('{quotation}', [QuotationController::class, 'update'])->name('update');
    Route::patch('{quotation}/convert', [QuotationController::class, 'convert'])->name('convert');
    Route::delete('{quotation}', [QuotationController::class, 'destroy'])->name('destroy');
});

Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
    Route::get('create', [PurchaseOrderController::class, 'create'])->name('create');
    Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
    Route::get('{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
    Route::put('{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('update');
    Route::patch('{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('receive');
    Route::delete('{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('destroy');
});
