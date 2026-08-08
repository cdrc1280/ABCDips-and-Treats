<?php

namespace App\Providers;

use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register security headers middleware for web and API routes
        try {
            $router = $this->app->make(\Illuminate\Routing\Router::class);
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\SecurityHeaders::class);
            $router->pushMiddlewareToGroup('api', \App\Http\Middleware\SecurityHeaders::class);
        } catch (\Throwable $e) {
            // If router isn't available during certain artisan commands, skip
        }

        Notifications::alignment(Alignment::End);
        Notifications::verticalAlignment(VerticalAlignment::Start);
    }
}
