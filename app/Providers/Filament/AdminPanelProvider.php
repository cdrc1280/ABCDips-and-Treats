<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('ABCDips & Treats')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                'primary' => Color::hex('#5C3A22'), // brand-choco
                'secondary' => Color::hex('#C08E5D'), // brand-caramel
                'gray' => Color::hex('#8C7A68'), // warm-gray
                'info' => Color::hex('#D9A876'), // brand-tan
                'success' => Color::hex('#6B8F5E'), // desaturated green
                'warning' => Color::hex('#C98A3A'), // desaturated amber
                'danger' => Color::hex('#B84C3C'), // desaturated red
            ])
            // ─── Auto-discover resources, pages, widgets ───────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                Dashboard::class,
            ])
            // ->widgets([
            //     AccountWidget::class,
            // ])
            // ─── Navigation ────────────────────────────────────────────
            ->navigationGroups([
                'Inventory & Supplies',
                'Products & Recipe Costing',
                'Orders & Sales',
                'Production & Purchasing',
                'HR & Payroll',
                'Engagement & Content',
                'System Administration',
            ])
            // ─── Database Notifications (Real-time Seller Order Alerts) ──
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')
            // ─── Auth ──────────────────────────────────────────────────
            ->authGuard('web')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
