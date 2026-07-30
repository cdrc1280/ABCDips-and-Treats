<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredientResource\Pages\CreateIngredient;
use App\Filament\Resources\IngredientResource\Pages\EditIngredient;
use App\Filament\Resources\IngredientResource\Pages\ListIngredients;
use App\Models\Ingredient;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

use Illuminate\Support\Str;

class IngredientResource extends Resource
{
    protected static ?string $model = Ingredient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ingredient Details')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('sku')
                            ->label('SKU (Auto-generated)')
                            ->default(fn() => 'ING-' . strtoupper(Str::random(6)))
                            ->readOnly()
                            ->required()
                            ->unique(Ingredient::class, 'sku', ignoreRecord: true),
                        TextInput::make('name')->required(),
                        Select::make('unit')
                            ->options(['kg' => 'Kilograms (kg)', 'g' => 'Grams (g)', 'L' => 'Liters (L)', 'ml' => 'Milliliters (ml)', 'pcs' => 'Pieces (pcs)', 'box' => 'Box'])
                            ->required(),
                        TextInput::make('cost_per_unit')->numeric()->prefix('₱')->required(),
                        TextInput::make('stock_qty')->label('Current Stock Qty')->numeric()->required()->default(0),
                        TextInput::make('min_stock_qty')->label('Min Reorder Level')->numeric()->required()->default(5),
                        TextInput::make('supplier_name'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->searchable()->sortable()->weight('bold'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('stock_qty')
                    ->label('Stock Qty')
                    ->sortable()
                    ->formatStateUsing(fn($state, Ingredient $record) => "{$state} {$record->unit}")
                    ->color(fn(Ingredient $record) => $record->is_low_stock ? 'danger' : 'success'),
                TextColumn::make('cost_per_unit')
                    ->label('Cost / Unit')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('supplier_name')->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('low_stock')
                    ->label('Low Stock Alert Only')
                    ->query(fn($query) => $query->whereColumn('stock_qty', '<=', 'min_stock_qty')),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIngredients::route('/'),
            'create' => CreateIngredient::route('/create'),
            'edit' => EditIngredient::route('/{record}/edit'),
        ];
    }
}
