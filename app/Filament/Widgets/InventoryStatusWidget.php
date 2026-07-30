<?php

namespace App\Filament\Widgets;

use App\Models\Ingredient;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class InventoryStatusWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = '📦 Raw Ingredients Stock Status';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ingredient::query()->orderBy('stock_qty', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Ingredient Name')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('stock_qty')
                    ->label('Stock')
                    ->formatStateUsing(fn($state, Ingredient $record) => "{$state} {$record->unit}")
                    ->color(fn(Ingredient $record) => $record->stock_qty <= $record->min_stock_qty ? 'danger' : 'success')
                    ->badge(),

                TextColumn::make('cost_per_unit')
                    ->label('Cost/Unit')
                    ->money('PHP'),
            ])
            ->paginated([5]);
    }
}
