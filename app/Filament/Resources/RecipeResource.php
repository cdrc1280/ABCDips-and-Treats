<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages\CreateRecipe;
use App\Filament\Resources\RecipeResource\Pages\EditRecipe;
use App\Filament\Resources\RecipeResource\Pages\ListRecipes;
use App\Models\Ingredient;
use App\Models\PackagingMaterial;
use App\Models\Recipe;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Products & Recipe Costing';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section 1: Recipe & Product Specifications
                Section::make('Recipe Specifications')
                    ->columnSpanFull()
                    ->components([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->unique(Recipe::class, 'product_id', ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Recipe / Batch Name')
                            ->placeholder('e.g. Choco Moist Batch')
                            ->required(),
                        TextInput::make('yield_qty')
                            ->label('Batch Yield Qty (Pieces / Tubs)')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->live()
                            ->extraInputAttributes(['inputmode' => 'numeric']),
                        TextInput::make('prep_time_minutes')
                            ->label('Prep Time (Minutes)')
                            ->numeric()
                            ->default(20)
                            ->extraInputAttributes(['inputmode' => 'numeric']),
                        TextInput::make('baking_time_minutes')
                            ->label('Baking Time (Minutes)')
                            ->numeric()
                            ->default(40)
                            ->extraInputAttributes(['inputmode' => 'numeric']),
                        RichEditor::make('instructions')
                            ->columnSpanFull(),
                    ])->columns(2),

                // Section 2: Raw Ingredients Breakdown (Excel Reference)
                Section::make('Bill of Materials (BOM) — Raw Ingredients Breakdown')
                    ->description('Enter raw ingredients, package costs, and quantities used per batch.')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('recipeIngredients')
                            ->relationship()
                            ->components([
                                Select::make('ingredient_id')
                                    ->label('Ingredient Item')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $ing = Ingredient::find($state);
                                            if ($ing) {
                                                $set('unit', $ing->unit);
                                            }
                                        }
                                    }),
                                TextInput::make('qty_required')
                                    ->label('Unit Amount Used')
                                    ->numeric()
                                    ->required()
                                    ->live()
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                Select::make('unit')
                                    ->label('Unit')
                                    ->options([
                                        'g'     => 'Grams (g)',
                                        'kg'    => 'Kilograms (kg)',
                                        'ml'    => 'Milliliters (ml)',
                                        'L'     => 'Liters (L)',
                                        'pcs'   => 'Pieces (pcs)',
                                        'cup'   => 'Cups',
                                        'tbsp'  => 'Tablespoons (tbsp)',
                                        'tsp'   => 'Teaspoons (tsp)',
                                    ])
                                    ->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),

                // Section 3: Packaging Materials Breakdown (PACKAGING Section in Excel)
                Section::make('PACKAGING — Packaging Materials Breakdown')
                    ->description('Select or enter packaging materials used for this batch (boxes, stickers, containers, bags).')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('recipePackagings')
                            ->relationship()
                            ->components([
                                Select::make('packaging_material_id')
                                    ->label('Select Packaging Item (Optional)')
                                    ->options(PackagingMaterial::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $pm = PackagingMaterial::find($state);
                                            if ($pm) {
                                                $set('name', $pm->name);
                                                $set('unit', $pm->unit);
                                                $set('package_qty', 1);
                                                $set('package_cost', $pm->cost_per_unit);
                                            }
                                        }
                                    }),
                                TextInput::make('name')
                                    ->label('Material Name')
                                    ->placeholder('e.g. Box, Plastic large, Logo')
                                    ->required(),
                                TextInput::make('unit')
                                    ->label('Unit')
                                    ->default('piece')
                                    ->required(),
                                TextInput::make('package_qty')
                                    ->label('Item Package Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('package_cost')
                                    ->label('Item Package Cost (₱)')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->default(0)
                                    ->required()
                                    ->live()
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('qty_used')
                                    ->label('Unit Amount Used')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),

                // Section 4: Excel Costing & Financial Computation Summary
                Section::make('Financial & Pricing Computation (Excel Business Costing Formula)')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('overhead_pct')
                            ->label('OVERHEAD COST %')
                            ->numeric()
                            ->suffix('%')
                            ->default(40.00)
                            ->required()
                            ->live()
                            ->extraInputAttributes(['inputmode' => 'decimal']),
                        TextInput::make('markup_pct')
                            ->label('MARK UP %')
                            ->numeric()
                            ->suffix('%')
                            ->default(66.00)
                            ->required()
                            ->live()
                            ->extraInputAttributes(['inputmode' => 'decimal']),
                        TextInput::make('labor_pct')
                            ->label('LABOR CHARGE %')
                            ->numeric()
                            ->suffix('%')
                            ->default(60.00)
                            ->required()
                            ->live()
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        Placeholder::make('excel_costing_summary')
                            ->label('Excel Financial Computation Live Summary')
                            ->columnSpanFull()
                            ->content(function ($record, callable $get) {
                                $overheadPct = (float) ($get('overhead_pct') ?? 40.00);
                                $markupPct   = (float) ($get('markup_pct') ?? 66.00);
                                $laborPct    = (float) ($get('labor_pct') ?? 60.00);
                                $yieldQty    = max(1, (int) ($get('yield_qty') ?? 1));

                                $totalIngredient = 0.0;
                                $rawIngredients  = $get('recipeIngredients') ?? [];
                                foreach ($rawIngredients as $ing) {
                                    if (! empty($ing['ingredient_id']) && ! empty($ing['qty_required'])) {
                                        $ingModel = Ingredient::find($ing['ingredient_id']);
                                        if ($ingModel) {
                                            $costPerUnit = (float) $ingModel->cost_per_unit;
                                            $qty         = (float) $ing['qty_required'];
                                            $recipeUnit  = strtolower(trim($ing['unit'] ?? 'g'));
                                            $stockUnit   = strtolower(trim($ingModel->unit));

                                            $mult = match ($recipeUnit) {
                                                'g', 'gram', 'grams' => in_array($stockUnit, ['kg', 'kilogram']) ? 0.001 : 1.0,
                                                'ml'                => in_array($stockUnit, ['l', 'liter']) ? 0.001 : 1.0,
                                                default             => 1.0,
                                            };
                                            $totalIngredient += ($qty * $mult) * $costPerUnit;
                                        }
                                    }
                                }

                                $totalPackaging = 0.0;
                                $rawPackaging   = $get('recipePackagings') ?? [];
                                foreach ($rawPackaging as $pkg) {
                                    $pkgQty  = max(1, (float) ($pkg['package_qty'] ?? 1));
                                    $cost    = (float) ($pkg['package_cost'] ?? 0);
                                    $used    = (float) ($pkg['qty_used'] ?? 0);
                                    $totalPackaging += ($cost / $pkgQty) * $used;
                                }

                                $totalRawCost   = $totalIngredient + $totalPackaging;
                                $overheadAmount = $totalRawCost * ($overheadPct / 100);
                                $totalCost      = $totalRawCost + $overheadAmount;
                                $markupAmount   = $totalCost * ($markupPct / 100);
                                $laborAmount    = $totalRawCost * ($laborPct / 100);
                                $batchPrice     = $totalCost + $markupAmount + $laborAmount;
                                $pricePerPiece  = $batchPrice / $yieldQty;

                                return new \Illuminate\Support\HtmlString("
                                    <div class='p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 space-y-2 text-sm'>
                                        <div class='grid grid-cols-1 md:grid-cols-3 gap-3 font-semibold'>
                                            <div>Total Ingredient Cost: <span class='text-amber-700 dark:text-amber-400 font-bold'>₱" . number_format($totalIngredient, 2) . "</span></div>
                                            <div>Total Packaging Cost: <span class='text-blue-700 dark:text-blue-400 font-bold'>₱" . number_format($totalPackaging, 2) . "</span></div>
                                            <div>Total Raw Materials Cost: <span class='text-gray-900 dark:text-white font-black'>₱" . number_format($totalRawCost, 2) . "</span></div>
                                        </div>
                                        <div class='grid grid-cols-1 md:grid-cols-3 gap-3 text-xs text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-gray-700 pt-2'>
                                            <div>Overhead Cost ({$overheadPct}%): ₱" . number_format($overheadAmount, 2) . "</div>
                                            <div>Mark Up ({$markupPct}%): ₱" . number_format($markupAmount, 2) . "</div>
                                            <div>Labor Charge ({$laborPct}%): ₱" . number_format($laborAmount, 2) . "</div>
                                        </div>
                                        <div class='flex flex-wrap items-center justify-between gap-4 border-t-2 border-amber-300 dark:border-amber-700 pt-3 mt-1'>
                                            <div>
                                                <span class='text-xs text-gray-500 uppercase font-bold block'>TOTAL BATCH SELLING PRICE</span>
                                                <span class='text-xl font-black text-gray-900 dark:text-white'>₱" . number_format($batchPrice, 2) . "</span>
                                            </div>
                                            <div class='bg-emerald-600 text-white px-4 py-2 rounded-xl text-center shadow-sm'>
                                                <span class='text-[10px] uppercase font-extrabold block text-emerald-100'>PRICE PER PIECE / TUB ({$yieldQty} YIELD)</span>
                                                <span class='text-2xl font-black'>₱" . number_format($pricePerPiece, 2) . "</span>
                                            </div>
                                        </div>
                                    </div>
                                ");
                            }),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('product.name')
                    ->searchable()
                    ->label('Product'),
                TextColumn::make('yield_qty')
                    ->label('Yield (Pcs/Tubs)'),
                TextColumn::make('total_raw_materials_cost')
                    ->label('Total Raw Cost')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('unit_selling_price')
                    ->label('Price / Piece (Tub)')
                    ->money('PHP')
                    ->weight('black')
                    ->color('success'),
                TextColumn::make('product.price')
                    ->label('Current Store Price')
                    ->money('PHP'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                Action::make('set_default_price')
                    ->label('Set as Product Default Price')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Set Product Selling Price')
                    ->modalDescription(fn(Recipe $record) => "Are you sure you want to set {$record->product->name} default price to ₱" . number_format($record->unit_selling_price, 2) . " per piece?")
                    ->action(function (Recipe $record) {
                        if ($record->product) {
                            $oldPrice = $record->product->price;
                            $newPrice = round($record->unit_selling_price, 2);
                            $record->product->price = $newPrice;
                            if ($record->product->sale_price !== null && $record->product->sale_price >= $newPrice) {
                                $record->product->sale_price = null;
                            }
                            $record->product->save();

                            Notification::make()
                                ->title("Default Product Price Updated!")
                                ->body("Set {$record->product->name} selling price to ₱" . number_format($newPrice, 2) . " per piece (previous: ₱" . number_format($oldPrice, 2) . ").")
                                ->success()
                                ->send();
                        }
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRecipes::route('/'),
            'create' => CreateRecipe::route('/create'),
            'edit'   => EditRecipe::route('/{record}/edit'),
        ];
    }
}
