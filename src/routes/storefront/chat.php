<?php

use App\Domains\Storefront\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::prefix('chat')->name('chat.')->controller(ChatController::class)->group(function () {
    Route::get('/', 'show')->middleware('throttle:60,1')->name('show');
    Route::post('messages', 'send')->middleware('throttle:20,1')->name('send');
    Route::post('escalate', 'escalate')->middleware('throttle:6,1')->name('escalate');
});
