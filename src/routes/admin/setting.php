<?php

use App\Domains\Setting\Controllers\StoreSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('settings', [StoreSettingsController::class, 'edit'])->name('settings.edit');
Route::put('settings', [StoreSettingsController::class, 'update'])->name('settings.update');
