<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return 'Monthly Revenue Trend (₱)';
    }

    protected function getData(): array
    {
        $analytics = app(AnalyticsService::class);
        $chart = $analytics->getMonthlyRevenueChartData();

        return [
            'datasets' => [
                [
                    'label' => 'Gross Revenue (₱)',
                    'data' => $chart['series'][0]['data'],
                    'backgroundColor' => '#D9A876',
                    'borderColor' => '#5C3A22',
                ],
            ],
            'labels' => $chart['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
