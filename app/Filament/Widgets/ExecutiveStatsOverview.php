<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\PackagingMaterial;
use App\Models\Payroll;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExecutiveStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $completedOrdersQuery = Order::where('status', Order::STATUS_COMPLETED);
        $totalRevenue = (float) $completedOrdersQuery->sum('total');
        $completedCount = $completedOrdersQuery->count();
        $totalOrdersCount = Order::count();

        $aov = $completedCount > 0 ? $totalRevenue / $completedCount : 0;

        $lowStockIngredients = Ingredient::whereColumn('stock_qty', '<=', 'min_stock_qty')->count();
        $lowStockPackaging   = PackagingMaterial::whereColumn('stock_qty', '<=', 'min_stock_qty')->count();
        $totalLowStock = $lowStockIngredients + $lowStockPackaging;

        $activeCoupons = Coupon::where('is_active', true)->count();
        $couponRedemptions = Coupon::sum('used_count');

        $totalPayrollDisbursed = (float) Payroll::sum('total_net');

        return [
            Stat::make('Total Revenue', '₱' . number_format($totalRevenue, 2))
                ->description('Fulfilled online & POS sales')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Orders & AOV', number_format($totalOrdersCount) . ' orders')
                ->description('Avg Order: ₱' . number_format($aov, 2))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),

            Stat::make('Low Stock Alerts', $totalLowStock . ' items')
                ->description("{$lowStockIngredients} ingredients, {$lowStockPackaging} packaging")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalLowStock > 0 ? 'danger' : 'success'),

            Stat::make('Active Vouchers', $activeCoupons . ' coupons')
                ->description(number_format($couponRedemptions) . ' total redemptions')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),

            Stat::make('Payroll Expenditure', '₱' . number_format($totalPayrollDisbursed, 2))
                ->description('Total net staff compensation')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
