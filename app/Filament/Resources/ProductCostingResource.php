<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCostingResource\Pages\CreateProductCosting;
use App\Filament\Resources\ProductCostingResource\Pages\EditProductCosting;
use App\Filament\Resources\ProductCostingResource\Pages\ListProductCostings;
use App\Models\CostingItem;
use App\Models\Ingredient;
use App\Models\PackagingMaterial;
use App\Models\Product;
use App\Models\ProductCosting;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProductCostingResource extends Resource
{
    protected static ?string $model = ProductCosting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'Products & Recipe Costing';

    protected static ?string $navigationLabel = 'Product Costing';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([

                // LEFT MAIN COLUMN (Spans 2 Cols)
                Group::make()
                    ->columnSpan(2)
                    ->components([

                        // 1. Section "Product"
                        Section::make('Product')
                            ->description('Select a product to calculate costing for financial reference.')
                            ->components([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live(onBlur: false)
                                    ->createOptionForm([
                                        Select::make('category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->required(),
                                        TextInput::make('name')
                                            ->label('Product Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                        TextInput::make('slug')
                                            ->required()
                                            ->readOnly()
                                            ->unique(Product::class, 'slug')
                                            ->maxLength(255),
                                        TextInput::make('sku')
                                            ->label('SKU (Auto-generated)')
                                            ->default(fn() => 'SKU-' . strtoupper(\Illuminate\Support\Str::random(6)))
                                            ->readOnly()
                                            ->required()
                                            ->unique(Product::class, 'sku'),
                                        TextInput::make('price')
                                            ->label('Initial Price')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->default(0.00)
                                            ->dehydrated(true)
                                            ->helperText('Manual initial selling price for reference.'),
                                    ]),
                                TextInput::make('yield_qty')
                                    ->label('Yield (pieces / tubs produced)')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live(onBlur: false)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('yield_unit')
                                    ->label('Yield unit label')
                                    ->default('tub (70g)')
                                    ->required(),
                            ])->columns(3),

                        // 2. Section "Raw materials"
                        Section::make('Raw materials')
                            ->description('Select ingredients directly from inventory database. Package price ÷ package amount = cost per unit, × qty used = line cost.')
                            ->components([
                                Repeater::make('ingredients')
                                    ->relationship('ingredients')
                                    ->label('Ingredients')
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        $data['group'] = 'ingredient';
                                        return $data;
                                    })
                                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                        $data['group'] = 'ingredient';
                                        return $data;
                                    })
                                    ->components([
                                        Select::make('ingredient_id')
                                            ->label('Select Ingredient')
                                            ->relationship('ingredient', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live(onBlur: false)
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $ing = Ingredient::find($state);
                                                    if ($ing) {
                                                        $rawUnit = strtolower(trim($ing->unit));
                                                        $unitMap = match ($rawUnit) {
                                                            'ml', 'l', 'liter', 'liters' => 'ml',
                                                            'pcs', 'piece', 'pieces'      => 'piece',
                                                            default                       => 'grams',
                                                        };
                                                        $set('unit', $unitMap);

                                                        $pkgAmt = in_array($rawUnit, ['kg', 'l', 'liter', 'liters']) ? 1000 : 1;
                                                        $set('package_amount', $pkgAmt);

                                                        $costPerUnit = (float) $ing->cost_per_unit;
                                                        $set('package_price', round($costPerUnit * $pkgAmt, 2));
                                                    }
                                                }
                                            })
                                            ->columnSpan(4),
                                        Select::make('unit')
                                            ->label('Unit')
                                            ->options([
                                                'grams' => 'Grams',
                                                'ml'    => 'ML',
                                                'piece' => 'Piece',
                                            ])
                                            ->default('grams')
                                            ->required()
                                            ->columnSpan(2),
                                        TextInput::make('package_amount')
                                            ->label('Package amt')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->live(onBlur: false)
                                            ->extraInputAttributes(['inputmode' => 'decimal'])
                                            ->columnSpan(2),
                                        TextInput::make('package_price')
                                            ->label('Package price')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->default(0)
                                            ->required()
                                            ->live(onBlur: false)
                                            ->extraInputAttributes(['inputmode' => 'decimal'])
                                            ->columnSpan(2),
                                        Placeholder::make('price_per_unit')
                                            ->label('₱/unit')
                                            ->content(function (callable $get) {
                                                $amt   = (float) ($get('package_amount') ?? 0);
                                                $price = (float) ($get('package_price') ?? 0);
                                                $ppu   = $amt > 0 ? ($price / $amt) : 0;
                                                return number_format($ppu, 4);
                                            })
                                            ->columnSpan(1),
                                        TextInput::make('qty_used')
                                            ->label('Qty used')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->live(onBlur: false)
                                            ->extraInputAttributes(['inputmode' => 'decimal'])
                                            ->columnSpan(1),
                                        Placeholder::make('line_cost')
                                            ->label('Line cost')
                                            ->content(function (callable $get) {
                                                $amt   = (float) ($get('package_amount') ?? 0);
                                                $price = (float) ($get('package_price') ?? 0);
                                                $qty   = (float) ($get('qty_used') ?? 0);
                                                $ppu   = $amt > 0 ? ($price / $amt) : 0;
                                                $cost  = $ppu * $qty;
                                                return '₱' . number_format($cost, 2);
                                            })
                                            ->columnSpan(1),
                                    ])
                                    ->columns(13)
                                    ->columnSpanFull()
                                    ->defaultItems(1),
                            ]),

                        // 3. Section "Packaging"
                        Section::make('Packaging')
                            ->description('Select packaging items directly from packaging materials database.')
                            ->components([
                                Repeater::make('packagings')
                                    ->relationship('packagings')
                                    ->label('Packaging Items')
                                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                        $data['group'] = 'packaging';
                                        return $data;
                                    })
                                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                        $data['group'] = 'packaging';
                                        return $data;
                                    })
                                    ->components([
                                        Select::make('packaging_material_id')
                                            ->label('Select Packaging Material')
                                            ->relationship('packagingMaterial', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live(onBlur: false)
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $pm = PackagingMaterial::find($state);
                                                    if ($pm) {
                                                        $rawUnit = strtolower(trim($pm->unit));
                                                        $unitMap = match ($rawUnit) {
                                                            'grams', 'g' => 'grams',
                                                            'ml', 'l'    => 'ml',
                                                            default      => 'piece',
                                                        };
                                                        $set('unit', $unitMap);
                                                        $set('package_amount', 1);
                                                        $set('package_price', (float) $pm->cost_per_unit);
                                                    }
                                                }
                                            })
                                            ->columnSpan(4),
                                        Select::make('unit')
                                            ->label('Unit')
                                            ->options([
                                                'piece' => 'Piece',
                                                'grams' => 'Grams',
                                                'ml'    => 'ML',
                                            ])
                                            ->default('piece')
                                            ->required()
                                            ->columnSpan(2),
                                        TextInput::make('package_amount')
                                            ->label('Package amt')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live(onBlur: false)
                                            ->extraInputAttributes(['inputmode' => 'decimal'])
                                            ->columnSpan(2),
                                        TextInput::make('package_price')
                                            ->label('Package price')
                                            ->numeric()
                                            ->prefix('₱')
                                            ->default(0)
                                            ->required()
                                            ->live(onBlur: false)
                                            ->extraInputAttributes(['inputmode' => 'decimal'])
                                            ->columnSpan(2),
                                        Placeholder::make('price_per_unit')
                                            ->label('₱/unit')
                                            ->content(function (callable $get) {
                                                $amt   = (float) ($get('package_amount') ?? 0);
                                                $price = (float) ($get('package_price') ?? 0);
                                                $ppu   = $amt > 0 ? ($price / $amt) : 0;
                                                return number_format($ppu, 4);
                                            })
                                            ->columnSpan(1),
                                        TextInput::make('qty_used')
                                            ->label('Qty used')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live(onBlur: false)
                                            ->extraInputAttributes(['inputmode' => 'decimal'])
                                            ->columnSpan(1),
                                        Placeholder::make('line_cost')
                                            ->label('Line cost')
                                            ->content(function (callable $get) {
                                                $amt   = (float) ($get('package_amount') ?? 0);
                                                $price = (float) ($get('package_price') ?? 0);
                                                $qty   = (float) ($get('qty_used') ?? 0);
                                                $ppu   = $amt > 0 ? ($price / $amt) : 0;
                                                $cost  = $ppu * $qty;
                                                return '₱' . number_format($cost, 2);
                                            })
                                            ->columnSpan(1),
                                    ])
                                    ->columns(13)
                                    ->columnSpanFull()
                                    ->defaultItems(1),
                            ]),

                        // 4. Section "Cost formula settings"
                        Section::make('Cost formula settings')
                            ->description('These percentages drive the summary panel — tune per product line.')
                            ->components([
                                TextInput::make('overhead_pct')
                                    ->label('Overhead (% of raw cost)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(40)
                                    ->required()
                                    ->live(onBlur: false)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('markup_pct')
                                    ->label('Mark up (% of total cost)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(66)
                                    ->required()
                                    ->live(onBlur: false)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('labor_pct')
                                    ->label('Labor (% of raw cost)')
                                    ->numeric()
                                    ->suffix('%')
                                    ->default(60)
                                    ->required()
                                    ->live(onBlur: false)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                            ])->columns(3),

                    ]),

                // RIGHT SIDEBAR / SUMMARY COLUMN (Spans 1 Col)
                Group::make()
                    ->columnSpan(1)
                    ->components([
                        Section::make('Live cost summary')
                            ->description('Recalculates on every keystroke.')
                            ->columnSpanFull()
                            ->components([
                                Placeholder::make('live_summary_panel')
                                    ->label('')
                                    ->content(function (callable $get) {
                                        $ingredientsSubtotal = 0.0;
                                        $ingredientsState    = $get('ingredients') ?? [];
                                        foreach ($ingredientsState as $row) {
                                            $amt   = (float) ($row['package_amount'] ?? 0);
                                            $price = (float) ($row['package_price'] ?? 0);
                                            $qty   = (float) ($row['qty_used'] ?? 0);
                                            $ppu   = $amt > 0 ? ($price / $amt) : 0;
                                            $ingredientsSubtotal += $ppu * $qty;
                                        }

                                        $packagingSubtotal = 0.0;
                                        $packagingState    = $get('packagings') ?? [];
                                        foreach ($packagingState as $row) {
                                            $amt   = (float) ($row['package_amount'] ?? 0);
                                            $price = (float) ($row['package_price'] ?? 0);
                                            $qty   = (float) ($row['qty_used'] ?? 0);
                                            $ppu   = $amt > 0 ? ($price / $amt) : 0;
                                            $packagingSubtotal += $ppu * $qty;
                                        }

                                        $rawCost     = $ingredientsSubtotal + $packagingSubtotal;
                                        $overheadPct = (float) ($get('overhead_pct') ?? 40) / 100;
                                        $markupPct   = (float) ($get('markup_pct') ?? 66) / 100;
                                        $laborPct    = (float) ($get('labor_pct') ?? 60) / 100;
                                        $yieldQty    = (float) ($get('yield_qty') ?? 1);
                                        $yieldUnit   = $get('yield_unit') ?? 'tub (70g)';

                                        $overheadCost  = $rawCost * $overheadPct;
                                        $totalCost     = $rawCost + $overheadCost;
                                        $markup        = $totalCost * $markupPct;
                                        $laborCharge   = $rawCost * $laborPct;
                                        $sellingPrice  = $totalCost + $markup + $laborCharge;
                                        $pricePerPiece = $yieldQty > 0 ? ($sellingPrice / $yieldQty) : 0;

                                        return new HtmlString("
                                            <div class='space-y-3 text-sm'>
                                                <div class='flex justify-between items-center'><span class='text-gray-500 dark:text-gray-400'>Ingredients subtotal</span><span class='font-medium text-gray-900 dark:text-white'>₱" . number_format($ingredientsSubtotal, 2) . "</span></div>
                                                <div class='flex justify-between items-center'><span class='text-gray-500 dark:text-gray-400'>Packaging subtotal</span><span class='font-medium text-gray-900 dark:text-white'>₱" . number_format($packagingSubtotal, 2) . "</span></div>
                                                
                                                <div class='flex justify-between items-center pt-2.5 border-t border-gray-200 dark:border-gray-700'><span class='text-gray-700 dark:text-gray-300 font-semibold'>Total raw cost</span><span class='font-bold text-gray-900 dark:text-white'>₱" . number_format($rawCost, 2) . "</span></div>
                                                <div class='flex justify-between items-center'><span class='text-gray-500 dark:text-gray-400'>Overhead cost</span><span class='font-medium text-gray-900 dark:text-white'>₱" . number_format($overheadCost, 2) . "</span></div>
                                                
                                                <div class='flex justify-between items-center pt-2.5 border-t border-gray-200 dark:border-gray-700'><span class='text-gray-700 dark:text-gray-300 font-semibold'>Total cost</span><span class='font-bold text-gray-900 dark:text-white'>₱" . number_format($totalCost, 2) . "</span></div>
                                                <div class='flex justify-between items-center'><span class='text-gray-500 dark:text-gray-400'>Mark up</span><span class='font-medium text-gray-900 dark:text-white'>₱" . number_format($markup, 2) . "</span></div>
                                                <div class='flex justify-between items-center'><span class='text-gray-500 dark:text-gray-400'>Labor charge</span><span class='font-medium text-gray-900 dark:text-white'>₱" . number_format($laborCharge, 2) . "</span></div>
                                                
                                                <div class='flex justify-between items-center pt-2.5 border-t border-gray-200 dark:border-gray-700'><span class='text-gray-900 dark:text-white font-extrabold'>Selling price</span><span class='font-black text-gray-900 dark:text-white text-base'>₱" . number_format($sellingPrice, 2) . "</span></div>
                                                
                                                <div class='mt-4 p-4 rounded-xl bg-indigo-600 text-white shadow-md space-y-1'>
                                                    <div class='flex justify-between items-baseline'>
                                                        <span class='text-xs font-semibold text-indigo-100 uppercase tracking-wide'>Price per piece</span>
                                                        <span class='text-2xl font-black text-white'>₱" . number_format($pricePerPiece, 2) . "</span>
                                                    </div>
                                                    <p class='text-[11px] text-indigo-200 mt-1'>Selling price ÷ " . ($yieldQty > 0 ? number_format($yieldQty) : 0) . " {$yieldUnit}</p>
                                                </div>
                                            </div>
                                        ");
                                    }),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('yield_qty')
                    ->label('Yield')
                    ->formatStateUsing(fn($record) => number_format($record->yield_qty) . ' ' . $record->yield_unit),
                TextColumn::make('raw_cost')
                    ->label('Raw Cost')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('price_per_piece')
                    ->label('Price / Piece')
                    ->money('PHP')
                    ->weight('black')
                    ->badge()
                    ->color('indigo'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProductCostings::route('/'),
            'create' => CreateProductCosting::route('/create'),
            'edit'   => EditProductCosting::route('/{record}/edit'),
        ];
    }
}
