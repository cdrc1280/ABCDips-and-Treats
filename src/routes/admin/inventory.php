<?php

use App\Domains\Inventory\Controllers\IngredientController;
use App\Domains\Inventory\Controllers\PackagingMaterialController;
use App\Domains\Inventory\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('suppliers')->name('suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('create', [SupplierController::class, 'create'])->name('create');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::get('{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
    Route::put('{supplier}', [SupplierController::class, 'update'])->name('update');
    Route::delete('{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
});

Route::prefix('ingredients')->name('ingredients.')->group(function () {
    Route::get('/', [IngredientController::class, 'index'])->name('index');
    Route::get('create', [IngredientController::class, 'create'])->name('create');
    Route::post('/', [IngredientController::class, 'store'])->name('store');
    Route::get('{ingredient}/edit', [IngredientController::class, 'edit'])->name('edit');
    Route::put('{ingredient}', [IngredientController::class, 'update'])->name('update');
    Route::patch('{ingredient}/restock', [IngredientController::class, 'restock'])->name('restock');
    Route::delete('{ingredient}', [IngredientController::class, 'destroy'])->name('destroy');
});

Route::prefix('packaging-materials')->name('packaging-materials.')->group(function () {
    Route::get('/', [PackagingMaterialController::class, 'index'])->name('index');
    Route::get('create', [PackagingMaterialController::class, 'create'])->name('create');
    Route::post('/', [PackagingMaterialController::class, 'store'])->name('store');
    Route::get('{packagingMaterial}/edit', [PackagingMaterialController::class, 'edit'])->name('edit');
    Route::put('{packagingMaterial}', [PackagingMaterialController::class, 'update'])->name('update');
    Route::patch('{packagingMaterial}/toggle', [PackagingMaterialController::class, 'toggle'])->name('toggle');
    Route::patch('{packagingMaterial}/restock', [PackagingMaterialController::class, 'restock'])->name('restock');
    Route::delete('{packagingMaterial}', [PackagingMaterialController::class, 'destroy'])->name('destroy');
});
