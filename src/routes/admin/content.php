<?php

use App\Domains\Content\Controllers\BlogPostController;
use App\Domains\Content\Controllers\ChatEscalationController;
use App\Domains\Content\Controllers\ContactMessageController;
use App\Domains\Content\Controllers\SuggestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('blog-posts')->name('blog-posts.')->group(function () {
    Route::get('/', [BlogPostController::class, 'index'])->name('index');
    Route::get('create', [BlogPostController::class, 'create'])->name('create');
    Route::post('/', [BlogPostController::class, 'store'])->name('store');
    Route::get('{blogPost}/edit', [BlogPostController::class, 'edit'])->name('edit');
    Route::put('{blogPost}', [BlogPostController::class, 'update'])->name('update');
    Route::patch('{blogPost}/publish', [BlogPostController::class, 'publish'])->name('publish');
    Route::delete('{blogPost}', [BlogPostController::class, 'destroy'])->name('destroy');
});

Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
    Route::get('/', [ContactMessageController::class, 'index'])->name('index');
    Route::get('{contactMessage}/edit', [ContactMessageController::class, 'edit'])->name('edit');
    Route::put('{contactMessage}', [ContactMessageController::class, 'update'])->name('update');
    Route::patch('{contactMessage}/mark-read', [ContactMessageController::class, 'markRead'])->name('mark-read');
    Route::patch('{contactMessage}/mark-replied', [ContactMessageController::class, 'markReplied'])->name('mark-replied');
    Route::delete('{contactMessage}', [ContactMessageController::class, 'destroy'])->name('destroy');
});

Route::prefix('suggestions')->name('suggestions.')->group(function () {
    Route::get('/', [SuggestionController::class, 'index'])->name('index');
    Route::get('{suggestion}/edit', [SuggestionController::class, 'edit'])->name('edit');
    Route::put('{suggestion}', [SuggestionController::class, 'update'])->name('update');
    Route::patch('{suggestion}/mark-reviewing', [SuggestionController::class, 'markReviewing'])->name('mark-reviewing');
    Route::patch('{suggestion}/mark-implemented', [SuggestionController::class, 'markImplemented'])->name('mark-implemented');
    Route::delete('{suggestion}', [SuggestionController::class, 'destroy'])->name('destroy');
});

Route::prefix('chat-escalations')->name('chat-escalations.')->group(function () {
    Route::get('/', [ChatEscalationController::class, 'index'])->name('index');
    Route::get('{chatEscalation}/edit', [ChatEscalationController::class, 'edit'])->name('edit');
    Route::put('{chatEscalation}', [ChatEscalationController::class, 'update'])->name('update');
    Route::patch('{chatEscalation}/reply', [ChatEscalationController::class, 'reply'])->name('reply');
    Route::patch('{chatEscalation}/resolve', [ChatEscalationController::class, 'resolve'])->name('resolve');
    Route::patch('{chatEscalation}/reopen', [ChatEscalationController::class, 'reopen'])->name('reopen');
    Route::delete('{chatEscalation}', [ChatEscalationController::class, 'destroy'])->name('destroy');
});
