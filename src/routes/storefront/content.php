<?php

use App\Domains\Storefront\Controllers\ContentController;
use Illuminate\Support\Facades\Route;

Route::controller(ContentController::class)->group(function () {
    Route::get('about', 'about')->name('about');
    Route::get('faq', 'faq')->name('faq');
    Route::get('contact', 'contact')->name('contact');
    Route::post('contact', 'sendContact')->middleware('throttle:5,1')->name('contact.send');
    Route::get('suggestions', 'suggestions')->name('suggestions');
    Route::post('suggestions', 'sendSuggestion')->middleware('throttle:5,1')->name('suggestions.send');
    Route::get('blog', 'blog')->name('blog');
    Route::get('blog/{slug}', 'post')->name('blog.show');
    Route::get('custom-orders', 'customOrders')->name('custom-orders');
    Route::post('custom-orders', 'sendCustomOrder')->middleware(['auth', 'throttle:5,1'])->name('custom-orders.send');
    Route::get('{page}', 'page')->whereIn('page', ['privacy', 'terms'])->name('legal');
});
