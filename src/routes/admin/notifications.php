<?php

use App\Domains\User\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('read-all', 'readAll')->name('read-all');
    Route::post('{notification}/read', 'read')->whereUuid('notification')->name('read');
});
