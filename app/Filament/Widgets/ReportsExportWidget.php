<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ReportsExportWidget extends Widget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected string $view = 'filament.widgets.reports-export-widget';

    public function getReports(): array
    {
        return [
            [
                'key' => 'sales',
                'title' => 'Sales & Revenue Report',
                'description' => 'Detailed order transaction history, payment methods, discounts & grand totals.',
                'icon' => 'heroicon-o-document-text',
            ],
            [
                'key' => 'products',
                'title' => 'Product Performance & Best Sellers',
                'description' => 'Sales volume, stock levels, and gross revenue rankings per item.',
                'icon' => 'heroicon-o-shopping-bag',
            ],
            [
                'key' => 'inventory',
                'title' => 'Inventory & Low Stock Reorder Alerts',
                'description' => 'Raw ingredients, packaging materials, current stock, cost/unit & valuation.',
                'icon' => 'heroicon-o-cube',
            ],
            [
                'key' => 'production',
                'title' => 'Kitchen Production Batch Report',
                'description' => 'Oven baking runs, recipe batches, actual yield units & baker status.',
                'icon' => 'heroicon-o-fire',
            ],
            [
                'key' => 'payroll',
                'title' => 'Employee Payroll & HR Expenditure',
                'description' => 'Staff salaries, overtime pay, deductions, and net payouts history.',
                'icon' => 'heroicon-o-banknotes',
            ],
            [
                'key' => 'coupons',
                'title' => 'Coupon & Discount Usage Report',
                'description' => 'Voucher code redemptions, discount values, and validity status.',
                'icon' => 'heroicon-o-ticket',
            ],
        ];
    }
}
