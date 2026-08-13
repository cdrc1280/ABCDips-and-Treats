<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\AdminReportController;

// Admin & Customer Order Printable & Downloadable Invoice Routes
Route::get('/order-invoice/{order}/download', [OrderController::class, 'downloadInvoice'])->name('order.invoice.download')->whereNumber('order');
Route::get('/order-invoice/{order}', [OrderController::class, 'adminInvoice'])->name('order.invoice')->whereNumber('order');

// Login named route fallback for Auth middleware redirects
Route::get('/login', function () {
    return redirect('/login');
})->name('login');

// Admin Multi-Format Business Reports Center Download Route (PDF, Excel, Word)
Route::middleware(['auth', 'role:super_admin|admin'])->get('/admin-report-download/{type}/{format}', [AdminReportController::class, 'download'])->name('admin.reports.download');

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/account/profile?verified=1');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent to your email!']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// SPA catch-all — serves the Vue app for all non-API, non-admin routes
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|admin|sanctum|download-test-cases-pdf|order-invoice|admin-report-download|email).*$');
