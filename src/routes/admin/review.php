<?php

use App\Domains\Review\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::get('create', [ReviewController::class, 'create'])->name('create');
    Route::post('/', [ReviewController::class, 'store'])->name('store');
    Route::get('{review}/edit', [ReviewController::class, 'edit'])->name('edit');
    Route::put('{review}', [ReviewController::class, 'update'])->name('update');
    Route::patch('{review}/toggle', [ReviewController::class, 'toggle'])->name('toggle');
    Route::patch('{review}/feature', [ReviewController::class, 'feature'])->name('feature');
    Route::delete('{review}', [ReviewController::class, 'destroy'])->name('destroy');
});
