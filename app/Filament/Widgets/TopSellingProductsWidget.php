<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopSellingProductsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = '🏆 Top Selling Pastries & Dip Treats';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->withCount(['orderItems as total_qty' => function (Builder $query) {
                        $query->select(DB::raw('COALESCE(SUM(qty), 0)'));
                    }])
                    ->withSum('orderItems as gross_revenue', 'subtotal')
                    ->orderByDesc('total_qty')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge(),

                TextColumn::make('total_qty')
                    ->label('Units Sold')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('gross_revenue')
                    ->label('Gross Revenue')
                    ->money('PHP')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->paginated([5, 10]);
    }
}
