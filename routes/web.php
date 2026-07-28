<?php

use Illuminate\Support\Facades\Route;

// Download Test Cases PDF Route
Route::get('/download-test-cases-pdf', function () {
    $path = public_path('ABCDips_Treats_Test_Case_Suite.pdf');
    if (!file_exists($path)) {
        abort(404, 'PDF file not found.');
    }
    return response()->download($path, 'ABCDips_Treats_Test_Case_Suite.pdf', [
        'Content-Type' => 'application/pdf',
    ]);
});

// SPA catch-all — serves the Vue app for all non-API, non-admin routes
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api|admin|sanctum|download-test-cases-pdf).*$');
