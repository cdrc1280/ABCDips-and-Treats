<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
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
    protected static string|\UnitEnum|null $navigationGroup = 'Store';
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

                Section::make('Product Variations')
                    ->columnSpanFull()
                    ->description('Set up size, weight, or pieces options customers can choose from in the cart.')
                    ->components([
                        Select::make('variation_type')
                            ->label('Variation Type')
                            ->options([
                                'none'   => 'No Variations',
                                'weight' => 'Weight (e.g. 250g, 500g, 1kg)',
                                'pieces' => 'Pieces (e.g. 6 pcs, 12 pcs, 24 pcs)',
                                'size'   => 'Size (e.g. Small, Medium, Large)',
                            ])
                            ->default('none')
                            ->live()
                            ->required(),

                        Repeater::make('variations')
                            ->label('Variation Options')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Option Label (e.g. 250g, 6 pcs, Small)')
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
                            ->readOnly()
                            ->helperText('Automatically calculated & set by Product Costing.')
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
