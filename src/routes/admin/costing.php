<?php

use App\Domains\Costing\Controllers\ProductCostingController;
use App\Domains\Costing\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::prefix('recipes')->name('recipes.')->group(function () {
    Route::get('/', [RecipeController::class, 'index'])->name('index');
    Route::get('create', [RecipeController::class, 'create'])->name('create');
    Route::post('/', [RecipeController::class, 'store'])->name('store');
    Route::get('{recipe}/edit', [RecipeController::class, 'edit'])->name('edit');
    Route::put('{recipe}', [RecipeController::class, 'update'])->name('update');
    Route::patch('{recipe}/apply-price', [RecipeController::class, 'applyPrice'])->name('apply-price');
    Route::delete('{recipe}', [RecipeController::class, 'destroy'])->name('destroy');
});

Route::prefix('product-costings')->name('product-costings.')->group(function () {
    Route::get('/', [ProductCostingController::class, 'index'])->name('index');
    Route::get('create', [ProductCostingController::class, 'create'])->name('create');
    Route::post('/', [ProductCostingController::class, 'store'])->name('store');
    Route::get('{productCosting}/edit', [ProductCostingController::class, 'edit'])->name('edit');
    Route::put('{productCosting}', [ProductCostingController::class, 'update'])->name('update');
    Route::delete('{productCosting}', [ProductCostingController::class, 'destroy'])->name('destroy');
});
