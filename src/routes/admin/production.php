<?php

use App\Domains\Production\Controllers\ProductionBatchController;
use Illuminate\Support\Facades\Route;

Route::prefix('production-batches')->name('production-batches.')->group(function () {
    Route::get('/', [ProductionBatchController::class, 'index'])->name('index');
    Route::get('create', [ProductionBatchController::class, 'create'])->name('create');
    Route::post('/', [ProductionBatchController::class, 'store'])->name('store');
    Route::get('{productionBatch}/edit', [ProductionBatchController::class, 'edit'])->name('edit');
    Route::put('{productionBatch}', [ProductionBatchController::class, 'update'])->name('update');
    Route::patch('{productionBatch}/complete', [ProductionBatchController::class, 'complete'])->name('complete');
    Route::delete('{productionBatch}', [ProductionBatchController::class, 'destroy'])->name('destroy');
});
