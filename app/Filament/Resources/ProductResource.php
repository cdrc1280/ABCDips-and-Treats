<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Ingredient;
use App\Models\PackagingMaterial;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string|\UnitEnum|null $navigationGroup = 'Products & Recipe Costing';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(string $operation, $state, Set $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        TextInput::make('slug')
                            ->required()
                            ->readOnly()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->maxLength(255),

                        Textarea::make('short_description')
                            ->rows(2)
                            ->columnSpanFull(),

                        TextInput::make('flavor')
                            ->label('Product Flavor / Sub-variant')
                            ->placeholder('e.g. Belgian Dark Chocolate, Ube Halaya, Cinnamon Butter')
                            ->maxLength(100)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Media & Product Images')
                    ->columnSpanFull()
                    ->components([
                        FileUpload::make('image_path')
                            ->label('Primary Image')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->imageEditor()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public'),

                        FileUpload::make('gallery')
                            ->label('Gallery Images (Multiple Display Images)')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->openable()
                            ->downloadable()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('products/gallery')
                            ->visibility('public'),
                    ])->columns(2),

                Section::make('Flavor Options (Separate Flavor Encoding)')
                    ->columnSpanFull()
                    ->description('Set up separate flavor choices (e.g. Belgian Dark Chocolate, Strawberry, Ube Halaya) with optional price adjustments.')
                    ->components([
                        Repeater::make('flavors')
                            ->label('Selectable Flavor Variations')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Flavor Name (e.g. Strawberry, Dark Chocolate, Ube)')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('price_modifier')
                                    ->label('Flavor Price Adjustment (₱)')
                                    ->helperText('Use positive or negative value. 0 = same price.')
                                    ->numeric()
                                    ->default(0)
                                    ->step('0.01')
                                    ->prefix('₱')
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Flavor Option')
                            ->reorderable()
                            ->collapsible(),
                    ])->columns(1),

                Section::make('Product Variations (Weight, Grams, Pieces, Size)')
                    ->columnSpanFull()
                    ->description('Set up size, weight/grams, pieces, or custom options (e.g. 250g, 500g, 6 pcs) with optional price modifiers.')
                    ->components([
                        Select::make('variation_type')
                            ->label('Variation Type (e.g. Grams, Weight, Size, Pieces)')
                            ->options([
                                'none'      => 'No Variations',
                                'weight'    => 'Weight / Grams (e.g. 250g, 500g, 1kg)',
                                'pieces'    => 'Pieces (e.g. 6 pcs, 12 pcs, 24 pcs)',
                                'size'      => 'Size (e.g. Small, Medium, Large)',
                                'packaging' => 'Packaging (e.g. Solo Box, Sharing Box)',
                                'custom'    => '✏️ Custom Variation Type...',
                            ])
                            ->default('none')
                            ->live()
                            ->afterStateHydrated(function (Select $component, $state, callable $set) {
                                $presetKeys = ['none', 'weight', 'pieces', 'size', 'packaging'];
                                if (!empty($state) && !in_array($state, $presetKeys)) {
                                    $set('variation_type', 'custom');
                                    $set('custom_variation_name', $state);
                                }
                            })
                            ->dehydrateStateUsing(function ($state, callable $get) {
                                if ($state === 'custom') {
                                    return $get('custom_variation_name') ?: 'custom';
                                }
                                return $state;
                            })
                            ->required(),

                        TextInput::make('custom_variation_name')
                            ->label('Custom Variation Label')
                            ->placeholder('e.g. Dip Flavor, Frosting, Box Type, Grammage')
                            ->helperText('Customers will see this label on the storefront (e.g. "Select Dip Flavor").')
                            ->required(fn(callable $get) => $get('variation_type') === 'custom')
                            ->hidden(fn(callable $get) => $get('variation_type') !== 'custom')
                            ->dehydrated(false)
                            ->live(),

                        Repeater::make('variations')
                            ->label('Variation Options (Required when variation type is set)')
                            ->helperText('Define variation options (e.g. 250g, 500g, 6 pcs). The first option will be auto-selected by default for customers on the storefront so prices calculate instantly.')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Option Label (e.g. 250g, 6 pcs, Small, Solo Box)')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('price_modifier')
                                    ->label('Price Adjustment (₱)')
                                    ->helperText('Use positive or negative value. 0 = same price.')
                                    ->numeric()
                                    ->default(0)
                                    ->step('0.01')
                                    ->prefix('₱')
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                            ])
                            ->columns(2)
                            ->minItems(fn(callable $get) => ($get('variation_type') && $get('variation_type') !== 'none') ? 1 : 0)
                            ->addActionLabel('Add Option')
                            ->reorderable()
                            ->collapsible()
                            ->hidden(fn(callable $get) => $get('variation_type') === 'none' || !$get('variation_type')),
                    ])->columns(1),

                Section::make('Pricing & Inventory')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('sku')
                            ->label('SKU (Auto-generated)')
                            ->default(fn() => 'SKU-' . strtoupper(Str::random(6)))
                            ->readOnly()
                            ->required()
                            ->unique(Product::class, 'sku', ignoreRecord: true),

                        TextInput::make('barcode')
                            ->label('Barcode (Auto-generated)')
                            ->default(fn() => '200' . sprintf('%09d', rand(100000000, 999999999)))
                            ->readOnly()
                            ->nullable(),

                        TextInput::make('price')
                            ->label('Selling Price')
                            ->default(0.00)
                            ->dehydrated(true)
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->prefix('₱')
                            ->helperText('Encode selling price manually. Product Costing can be used as a financial reference.')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        TextInput::make('sale_price')
                            ->nullable()
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        DateTimePicker::make('sale_ends_at')
                            ->label('Sale Ends At')
                            ->nullable()
                            ->helperText('Leave empty for no expiry. Sale auto-expires at this date/time.')
                            ->minDate(now()),

                        TextInput::make('stock_qty')
                            ->label('Stock Quantity')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->default(50)
                            ->extraInputAttributes(['inputmode' => 'numeric']),

                        TextInput::make('min_stock_qty')
                            ->label('Minimum Stock Alert Level')
                            ->required()
                            ->integer()
                            ->minValue(0)
                            ->default(10)
                            ->extraInputAttributes(['inputmode' => 'numeric']),
                    ])->columns(2),

                Group::make()
                    ->relationship('recipe')
                    ->columnSpanFull()
                    ->components([
                        Section::make('Ingredients & Packaging (Real-Time Auto Stock Deduction BOM)')
                            ->description('Attach ingredients and packaging directly to this product. When customers order this product (online or POS), the system automatically deducts these items from raw inventory in real time.')
                            ->components([
                                Hidden::make('name')
                                    ->default('Product Recipe BOM'),
                                Hidden::make('yield_qty')
                                    ->default(1),

                                 Repeater::make('recipeIngredients')
                                    ->relationship('recipeIngredients')
                                    ->label('Raw Ingredients Breakdown')
                                    ->components([
                                        Select::make('ingredient_id')
                                            ->label('Select Ingredient')
                                            ->relationship('ingredient', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->createOptionForm([
                                                TextInput::make('sku')
                                                    ->label('SKU (Auto-generated)')
                                                    ->default(fn() => 'ING-' . strtoupper(Str::random(6)))
                                                    ->readOnly()
                                                    ->required(),
                                                TextInput::make('name')
                                                    ->label('Ingredient Name')
                                                    ->placeholder('e.g. Cocoa Powder, Flour, Milk')
                                                    ->required(),
                                                Select::make('unit')
                                                    ->label('Stock Measurement Unit')
                                                    ->options([
                                                        'grams' => 'Grams (grams / g)',
                                                        'ml'    => 'Milliliters (ml)',
                                                        'piece' => 'Piece (piece / pcs)',
                                                        'kg'    => 'Kilograms (kg)',
                                                        'L'     => 'Liters (L)',
                                                        'box'   => 'Box / Pack',
                                                    ])
                                                    ->default('grams')
                                                    ->required(),
                                                TextInput::make('item_unit')
                                                    ->label('Item Unit (Package Amount)')
                                                    ->numeric()
                                                    ->minValue(0.001)
                                                    ->default(1000)
                                                    ->required()
                                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                                TextInput::make('item_price')
                                                    ->label('Item Price (₱)')
                                                    ->numeric()
                                                    ->prefix('₱')
                                                    ->default(0)
                                                    ->required()
                                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                                TextInput::make('stock_qty')
                                                    ->label('Current Stock Qty')
                                                    ->numeric()
                                                    ->default(1000)
                                                    ->required()
                                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                                Select::make('supplier_id')
                                                    ->label('Supplier')
                                                    ->options(\App\Models\Supplier::query()->pluck('name', 'id'))
                                                    ->searchable()
                                                    ->preload(),
                                            ])
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $ing = Ingredient::find($state);
                                                    if ($ing) {
                                                        $rawUnit = strtolower(trim($ing->unit ?? 'grams'));
                                                        $unitMap = match ($rawUnit) {
                                                            'g', 'gram', 'grams'           => 'grams',
                                                            'ml', 'milliliter'             => 'ml',
                                                            'pc', 'pcs', 'piece', 'pieces' => 'piece',
                                                            'kg', 'kilogram', 'kilograms'  => 'kg',
                                                            'l', 'liter', 'liters'         => 'L',
                                                            default                        => $rawUnit ?: 'grams',
                                                        };
                                                        $set('unit', $unitMap);
                                                    }
                                                }
                                            }),
                                        TextInput::make('qty_required')
                                            ->label('Unit Amount Used (Per Batch)')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->extraInputAttributes(['inputmode' => 'decimal']),
                                        Select::make('unit')
                                            ->label('Unit')
                                            ->options([
                                                'grams' => 'Grams (grams / g)',
                                                'ml'    => 'Milliliters (ml)',
                                                'piece' => 'Piece (piece / pcs)',
                                                'kg'    => 'Kilograms (kg)',
                                                'L'     => 'Liters (L)',
                                                'cup'   => 'Cups',
                                                'tbsp'  => 'Tablespoons (tbsp)',
                                                'tsp'   => 'Teaspoons (tsp)',
                                            ])
                                            ->default('grams')
                                            ->required(),
                                    ])
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Ingredient Item'),

                                Repeater::make('recipePackagings')
                                    ->relationship('recipePackagings')
                                    ->label('Packaging Materials Breakdown')
                                    ->components([
                                        Select::make('packaging_material_id')
                                            ->label('Select Packaging Material')
                                            ->relationship('packagingMaterial', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->createOptionForm([
                                                TextInput::make('sku')
                                                    ->label('SKU (Auto-generated)')
                                                    ->default(fn() => 'PKG-' . strtoupper(Str::random(6)))
                                                    ->readOnly()
                                                    ->required(),
                                                TextInput::make('name')
                                                    ->label('Material Name')
                                                    ->placeholder('e.g. Container Box 70g')
                                                    ->required(),
                                                Select::make('type')
                                                    ->label('Material Type')
                                                    ->options([
                                                        'box'       => 'Box',
                                                        'bag'       => 'Bag',
                                                        'board'     => 'Board',
                                                        'container' => 'Container',
                                                        'ribbon'    => 'Ribbon',
                                                        'label'     => 'Label',
                                                        'sticker'   => 'Sticker',
                                                        'tape'      => 'Tape',
                                                        'wrap'      => 'Wrap',
                                                        'other'     => 'Other',
                                                    ])
                                                    ->default('box')
                                                    ->required(),
                                                Select::make('unit')
                                                    ->label('Measurement Unit')
                                                    ->options([
                                                        'piece' => 'Piece (pcs)',
                                                        'box'   => 'Box',
                                                        'bag'   => 'Bag',
                                                        'sheet' => 'Sheet',
                                                        'roll'  => 'Roll',
                                                    ])
                                                    ->default('piece')
                                                    ->required(),
                                                TextInput::make('cost_per_unit')
                                                    ->label('Cost Per Unit (₱)')
                                                    ->numeric()
                                                    ->prefix('₱')
                                                    ->default(0)
                                                    ->required()
                                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                                TextInput::make('stock_qty')
                                                    ->label('Current Stock Qty')
                                                    ->numeric()
                                                    ->default(500)
                                                    ->required()
                                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                            ])
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $pm = PackagingMaterial::find($state);
                                                    if ($pm) {
                                                        $set('name', $pm->name);
                                                        $rawUnit = strtolower(trim($pm->unit ?? 'piece'));
                                                        $unitMap = match ($rawUnit) {
                                                            'pcs', 'pc', 'piece', 'pieces' => 'piece',
                                                            'box', 'boxes'                => 'box',
                                                            'bag', 'bags'                 => 'bag',
                                                            'sheet', 'sheets'             => 'sheet',
                                                            'roll', 'rolls'               => 'roll',
                                                            'g', 'gram', 'grams'          => 'grams',
                                                            'ml', 'l'                     => 'ml',
                                                            default                       => $rawUnit ?: 'piece',
                                                        };
                                                        $set('unit', $unitMap);
                                                        $set('package_qty', 1);
                                                        $set('package_cost', $pm->cost_per_unit);
                                                    }
                                                }
                                            }),
                                        TextInput::make('name')
                                            ->label('Packaging Label')
                                            ->required(),
                                        TextInput::make('qty_used')
                                            ->label('Qty Used (Per Batch)')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->extraInputAttributes(['inputmode' => 'decimal']),
                                        Select::make('unit')
                                            ->label('Unit')
                                            ->options([
                                                'piece' => 'Piece (pcs)',
                                                'box'   => 'Box',
                                                'bag'   => 'Bag',
                                                'sheet' => 'Sheet',
                                                'roll'  => 'Roll',
                                                'grams' => 'Grams (g)',
                                                'ml'    => 'Milliliters (ml)',
                                            ])
                                            ->default('piece')
                                            ->required(),
                                    ])
                                    ->columns(4)
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Packaging Item'),
                            ])->columns(2),
                    ]),

                Section::make('Organization & Visibility')
                    ->columnSpanFull()
                    ->components([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('prep_time_minutes')
                            ->label('Prep Time (minutes)')
                            ->integer()
                            ->minValue(0)
                            ->default(30)
                            ->extraInputAttributes(['inputmode' => 'numeric']),

                        Toggle::make('is_active')
                            ->label('Active on Storefront')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Featured Product')
                            ->default(false),

                        Toggle::make('is_best_seller')
                            ->label('Best Seller (Force Override)')
                            ->helperText('Auto-detected via top sales volume. Turn ON to forcefully set as Best Seller.')
                            ->default(false),

                        Toggle::make('is_new_arrival')
                            ->label('New Arrival (Force Override)')
                            ->helperText('Auto-expires after 1 month from creation date. Turn ON to force keep as New Arrival.')
                            ->default(false),

                        Toggle::make('is_seasonal')
                            ->label('Seasonal Item')
                            ->default(false),

                        Toggle::make('is_limited')
                            ->label('Limited Edition')
                            ->default(false),
                    ])->columns(2),

                Section::make('SEO Metadata')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('seo_title')
                            ->maxLength(255),

                        Textarea::make('seo_description')
                            ->rows(3),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(asset('images/logo.png'))
                    ->getStateUsing(fn(Product $record) => $record->primary_image_url),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('flavor')
                    ->label('Flavor')
                    ->searchable()
                    ->placeholder('—')
                    ->badge()
                    ->color('warning')
                    ->toggleable(),

                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable()
                    ->badge(),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),

                TextColumn::make('sale_price')
                    ->money('PHP')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('stock_qty')
                    ->label('Stock')
                    ->sortable()
                    ->color(fn(Product $record) => $record->stock_qty <= $record->min_stock_qty ? 'danger' : 'success'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_featured')
                    ->label('Featured'),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('toggle_featured')
                        ->label(fn(Product $record) => $record->is_featured ? 'Unfeature' : 'Feature on Homepage ⭐')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->action(function (Product $record) {
                            $record->update(['is_featured' => !$record->is_featured]);
                            Notification::make()
                                ->title($record->is_featured ? 'Product Featured on Homepage ⭐' : 'Product Removed from Featured')
                                ->success()
                                ->send();
                        }),
                    Action::make('toggle_active')
                        ->label(fn(Product $record) => $record->is_active ? 'Deactivate' : 'Activate Product')
                        ->icon('heroicon-o-power')
                        ->color(fn(Product $record) => $record->is_active ? 'danger' : 'success')
                        ->action(function (Product $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            Notification::make()
                                ->title($record->is_active ? 'Product Activated for Sale 🟢' : 'Product Deactivated 🔴')
                                ->info()
                                ->send();
                        }),
                    Action::make('costing')
                        ->label('Costing')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->url(fn(Product $record) => "/admin/product-costings/create?product_id={$record->id}"),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
