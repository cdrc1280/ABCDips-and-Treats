<?php

use App\Domains\Audit\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
