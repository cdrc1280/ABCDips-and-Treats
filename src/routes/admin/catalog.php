<?php

use App\Domains\Catalog\Controllers\CategoryController;
use App\Domains\Catalog\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('{category}', [CategoryController::class, 'update'])->name('update');
    Route::patch('{category}/toggle', [CategoryController::class, 'toggle'])->name('toggle');
    Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('{product}', [ProductController::class, 'update'])->name('update');
    Route::patch('{product}/toggle', [ProductController::class, 'toggle'])->name('toggle');
    Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
});
