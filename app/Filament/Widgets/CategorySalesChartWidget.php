<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use App\Models\ProductCategory;
use Filament\Widgets\ChartWidget;

class CategorySalesChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Sales Breakdown by Category (₱)';
    }

    protected function getData(): array
    {
        $categories = ProductCategory::all();
        $labels = [];
        $data = [];
        $colors = ['#5C3A22', '#C08E5D', '#D9A876', '#6B8F5E', '#C98A3A', '#B84C3C'];

        foreach ($categories as $cat) {
            $catProductIds = $cat->products()->pluck('id');
            $revenue = (float) OrderItem::whereIn('product_id', $catProductIds)->sum('subtotal');
            $labels[] = $cat->name;
            $data[] = round($revenue, 2);
        }

        if (empty($labels)) {
            $labels = ['No Categories'];
            $data = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (₱)',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, max(1, count($data))),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
