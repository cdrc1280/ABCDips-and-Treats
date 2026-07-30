<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
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
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(5120),

                        FileUpload::make('gallery')
                            ->label('Gallery Images')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->disk('public')
                            ->directory('products/gallery')
                            ->maxSize(5120),
                    ])->columns(2),

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
                            ->required()
                            ->numeric()
                            ->prefix('₱'),

                        TextInput::make('sale_price')
                            ->nullable()
                            ->numeric()
                            ->prefix('₱'),

                        TextInput::make('stock_qty')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->default(50),

                        TextInput::make('min_stock_qty')
                            ->label('Minimum Stock Alert Level')
                            ->required()
                            ->numeric()
                            ->default(10),
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
                            ->numeric()
                            ->default(30),

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
                EditAction::make(),
                DeleteAction::make(),
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
