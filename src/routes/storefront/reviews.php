<?php

use App\Domains\Storefront\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::post('reviews', [ReviewController::class, 'store'])->middleware('throttle:6,1')->name('reviews.store');
Route::post('reviews/{review}/helpful', [ReviewController::class, 'helpful'])->middleware('throttle:25,1')->whereUuid('review')->name('reviews.helpful');
