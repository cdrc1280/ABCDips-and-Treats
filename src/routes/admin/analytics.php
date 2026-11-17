<?php

use App\Domains\Analytics\Controllers\AiAdvisorController;
use Illuminate\Support\Facades\Route;

Route::get('ai-advisor', [AiAdvisorController::class, 'index'])->name('ai-advisor.index');
Route::post('ai-advisor', [AiAdvisorController::class, 'ask'])->name('ai-advisor.ask')->middleware('throttle:30,1');
