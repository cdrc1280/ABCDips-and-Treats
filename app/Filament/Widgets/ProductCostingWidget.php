<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Services\CostingService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductCostingWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $products = Product::where('is_active', true)->with('recipe.recipeIngredients.ingredient')->get();
        if ($products->isEmpty()) {
            return [
                Stat::make('Bakery Costing Status', 'No Active Products'),
            ];
        }

        $service = app(CostingService::class);
        $totalFoodCostPct = 0.0;
        $totalGrossMargin = 0.0;
        $count = 0;

        $highestMarginProduct = null;
        $highestMarginPct     = -1.0;

        $lowestMarginProduct = null;
        $lowestMarginPct     = 999.0;

        foreach ($products as $p) {
            $costing = $service->calculateForProduct($p);
            $totalFoodCostPct += $costing['food_cost_pct'];
            $totalGrossMargin += $costing['gross_margin_pct'];
            $count++;

            if ($costing['gross_margin_pct'] > $highestMarginPct) {
                $highestMarginPct     = $costing['gross_margin_pct'];
                $highestMarginProduct = $p->name;
            }

            if ($costing['gross_margin_pct'] < $lowestMarginPct && $costing['current_price'] > 0) {
                $lowestMarginPct     = $costing['gross_margin_pct'];
                $lowestMarginProduct = $p->name;
            }
        }

        $avgFoodCostPct  = $count > 0 ? round($totalFoodCostPct / $count, 1) : 0.0;
        $avgGrossMargin  = $count > 0 ? round($totalGrossMargin / $count, 1) : 0.0;

        return [
            Stat::make('Average Bakery Food Cost', "{$avgFoodCostPct}%")
                ->description('Target Benchmark: 28% – 35%')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color($avgFoodCostPct <= 35 ? 'success' : 'warning'),

            Stat::make('Average Gross Margin', "{$avgGrossMargin}%")
                ->description('Portfolio Average Profit Margin')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgGrossMargin >= 50 ? 'success' : 'warning'),

            Stat::make('Most Profitable Item', $highestMarginProduct ? "{$highestMarginProduct} (" . round($highestMarginPct, 1) . "%)" : 'N/A')
                ->description('Highest Gross Margin %')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Lowest Margin Alert', $lowestMarginProduct ? "{$lowestMarginProduct} (" . round($lowestMarginPct, 1) . "%)" : 'N/A')
                ->description('Needs Price or Recipe Review')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowestMarginPct < 35 ? 'danger' : 'warning'),
        ];
    }
}
