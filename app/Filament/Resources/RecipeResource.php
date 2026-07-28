<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages\CreateRecipe;
use App\Filament\Resources\RecipeResource\Pages\EditRecipe;
use App\Filament\Resources\RecipeResource\Pages\ListRecipes;
use App\Models\Recipe;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Recipe Specifications')
                    ->components([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->unique(Recipe::class, 'product_id', ignoreRecord: true),
                        TextInput::make('name')->required(),
                        TextInput::make('yield_qty')->label('Standard Yield Qty')->numeric()->required()->default(1),
                        TextInput::make('prep_time_minutes')->numeric()->default(20),
                        TextInput::make('baking_time_minutes')->numeric()->default(40),
                        RichEditor::make('instructions')->columnSpanFull(),
                    ]),

                Section::make('Bill of Materials (BOM) — Ingredients Needed')
                    ->components([
                        Repeater::make('recipeIngredients')
                            ->relationship()
                            ->components([
                                Select::make('ingredient_id')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable(),
                                TextInput::make('qty_required')->numeric()->required(),
                                Select::make('unit')
                                    ->options(['g' => 'Grams (g)', 'kg' => 'Kilograms (kg)', 'ml' => 'Milliliters (ml)', 'L' => 'Liters (L)', 'pcs' => 'Pieces (pcs)'])
                                    ->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('product.name')->searchable()->label('Product'),
                TextColumn::make('yield_qty')->label('Yield'),
                TextColumn::make('calculated_cost')
                    ->label('Batch Cost')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('unit_cost')
                    ->label('Cost / Unit')
                    ->money('PHP'),
                TextColumn::make('product.price')
                    ->label('Selling Price')
                    ->money('PHP'),
                TextColumn::make('gross_margin_percentage')
                    ->label('Gross Margin')
                    ->formatStateUsing(fn($state) => "{$state}%")
                    ->badge()
                    ->color(fn($state) => $state >= 60 ? 'success' : ($state >= 40 ? 'warning' : 'danger')),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecipes::route('/'),
            'create' => CreateRecipe::route('/create'),
            'edit' => EditRecipe::route('/{record}/edit'),
        ];
    }
}
