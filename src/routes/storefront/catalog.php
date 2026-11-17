<?php

use App\Domains\Storefront\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

Route::controller(CatalogController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('shop', 'shop')->name('shop');
    Route::get('category/{slug}', 'category')->name('category');
    Route::get('products/{slug}', 'show')->name('products.show');
    Route::get('products/{slug}/quick-view', 'quickView')->middleware('throttle:120,1')->name('products.quick-view');
    Route::get('{collection}', 'collection')->whereIn('collection', ['featured', 'best-sellers', 'new-arrivals'])->name('collection');
});
