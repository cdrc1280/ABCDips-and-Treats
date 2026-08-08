<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\AdminReportController;

// Admin & Store Printable Order Invoice Route
Route::middleware(['auth', 'role:super_admin|admin'])->get('/order-invoice/{order}', [OrderController::class, 'adminInvoice'])->name('order.invoice');

// Admin Multi-Format Business Reports Center Download Route (PDF, Excel, Word)
Route::middleware(['auth', 'role:super_admin|admin'])->get('/admin-report-download/{type}/{format}', [AdminReportController::class, 'download'])->name('admin.reports.download');

// SPA catch-all — serves the Vue app for all non-API, non-admin routes
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|admin|sanctum|download-test-cases-pdf|order-invoice|admin-report-download).*$');
