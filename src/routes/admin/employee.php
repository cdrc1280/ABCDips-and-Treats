<?php

use App\Domains\Employee\Controllers\AttendanceController;
use App\Domains\Employee\Controllers\EmployeeController;
use App\Domains\Employee\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->name('employees.')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    Route::get('create', [EmployeeController::class, 'create'])->name('create');
    Route::post('/', [EmployeeController::class, 'store'])->name('store');
    Route::get('{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
    Route::put('{employee}', [EmployeeController::class, 'update'])->name('update');
    Route::patch('{employee}/toggle', [EmployeeController::class, 'toggle'])->name('toggle');
    Route::delete('{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
});

Route::prefix('attendances')->name('attendances.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('create', [AttendanceController::class, 'create'])->name('create');
    Route::post('/', [AttendanceController::class, 'store'])->name('store');
    Route::get('{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');
    Route::put('{attendance}', [AttendanceController::class, 'update'])->name('update');
    Route::delete('{attendance}', [AttendanceController::class, 'destroy'])->name('destroy');
});

Route::prefix('payrolls')->name('payrolls.')->group(function () {
    Route::get('/', [PayrollController::class, 'index'])->name('index');
    Route::get('create', [PayrollController::class, 'create'])->name('create');
    Route::post('/', [PayrollController::class, 'store'])->name('store');
    Route::get('{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
    Route::put('{payroll}', [PayrollController::class, 'update'])->name('update');
    Route::patch('{payroll}/approve', [PayrollController::class, 'approve'])->name('approve');
    Route::patch('{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('mark-paid');
    Route::delete('{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
});
