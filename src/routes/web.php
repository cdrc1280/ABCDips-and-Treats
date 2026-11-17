<?php

use App\Domains\Preference\Controllers\PreferenceController;
use App\Domains\Sales\Controllers\InvoiceController;
use App\Http\Controllers\AdminReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Global web routes
|--------------------------------------------------------------------------
| Auth pages (/login, /register, /forgot-password, /reset-password, /email/verify)
| come from Fortify. Back-office modules live in routes/admin/*.php under /admin.
*/

// Printable / downloadable invoices
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/order-invoice/{order}/download', [InvoiceController::class, 'download'])->name('order.invoice.download')->whereUuid('order');
    Route::get('/order-invoice/{order}', [InvoiceController::class, 'show'])->name('order.invoice')->whereUuid('order');
});

// Multi-format business reports (PDF, Excel, Word)
Route::middleware(['auth', 'can:reports-export'])
    ->get('/admin-report-download/{type}/{format}', [AdminReportController::class, 'download'])
    ->name('admin.reports.download');

// ─── Back office ──────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'can:dashboard-view'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::put('preferences/tables/{table}/columns', [PreferenceController::class, 'updateTableColumns'])
            ->name('preferences.table-columns');

        foreach (glob(__DIR__.'/admin/*.php') ?: [] as $moduleRoutes) {
            require $moduleRoutes;
        }
    });

// ─── Storefront ───────────────────────────────────────────────
foreach (glob(__DIR__.'/storefront/*.php') ?: [] as $storefrontRoutes) {
    require $storefrontRoutes;
}

// ─── Unknown URLs ─────────────────────────────────────────────
// A 404 for an unmatched URL normally skips the `web` middleware, so the branded error page
// rendered without session or shared props (auth, cart, store) and the storefront nav crashed.
// The fallback route runs inside the `web` group, so the 404 page gets the full shell.
Route::fallback(fn () => abort(404));
