<?php

use App\Domains\Pos\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('pos', [PosController::class, 'index'])->name('pos.index');
Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout')->middleware('throttle:60,1');
