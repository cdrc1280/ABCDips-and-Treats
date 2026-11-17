<?php

use App\Domains\Purchasing\Controllers\PurchaseOrderImportController;
use Illuminate\Support\Facades\Route;

Route::post('purchase-orders/import-pdf', PurchaseOrderImportController::class)->name('purchase-orders.import-pdf')->middleware('throttle:20,1');
