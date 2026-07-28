<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExecutiveStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $analytics = app(AnalyticsService::class);
        $summary = $analytics->getExecutiveSummary();

        return [
            Stat::make('Total Revenue', '₱' . number_format($summary['total_revenue'], 2))
                ->description('Completed sales & walk-in POS')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Orders', $summary['total_orders'])
                ->description("{$summary['completed_orders']} fulfilled")
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Low Stock Alerts', $summary['low_stock_alerts'])
                ->description('Raw ingredients below min limit')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($summary['low_stock_alerts'] > 0 ? 'danger' : 'success'),
        ];
    }
}
