<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredientResource\Pages\CreateIngredient;
use App\Filament\Resources\IngredientResource\Pages\EditIngredient;
use App\Filament\Resources\IngredientResource\Pages\ListIngredients;
use App\Models\Ingredient;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory & Supplies';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ingredient Costing & Inventory Details (PDF Breakdown Format)')
                    ->description('Enter package item unit, item price, and base measurement unit to automatically calculate price per unit.')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('sku')
                            ->label('SKU (Auto-generated)')
                            ->default(fn() => 'ING-' . strtoupper(Str::random(6)))
                            ->readOnly()
                            ->required()
                            ->unique(Ingredient::class, 'sku', ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Ingredient Name')
                            ->placeholder('e.g. All purpose flour, Cocoa, Butter')
                            ->required(),
                        Select::make('unit')
                            ->label('Unit (Stock Measurement)')
                            ->options([
                                'grams' => 'Grams (grams / g)',
                                'ml' => 'Milliliters (ml)',
                                'piece' => 'Piece (piece / pcs)',
                                'kg' => 'Kilograms (kg)',
                                'L' => 'Liters (L)',
                                'box' => 'Box / Pack',
                            ])
                            ->default('grams')
                            ->required(),
                        TextInput::make('item_unit')
                            ->label('Item Unit (Package Amount)')
                            ->numeric()
                            ->minValue(0.001)
                            ->default(1000)
                            ->required()
                            ->live(onBlur: false)
                            ->extraInputAttributes(['inputmode' => 'decimal'])
                            ->helperText('e.g., 1000 for 1000g flour, 1 for 1 pc egg, 380 for 380g evap'),
                        TextInput::make('item_price')
                            ->label('Item Price (Package Price ₱)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->default(0)
                            ->required()
                            ->live(onBlur: false)
                            ->extraInputAttributes(['inputmode' => 'decimal'])
                            ->helperText('e.g., ₱65 for 1000g flour package'),
                        Placeholder::make('price_unit')
                            ->label('Price Unit (₱ / Base Unit)')
                            ->content(function (callable $get) {
                                $itemUnit = (float) ($get('item_unit') ?? 1);
                                $itemPrice = (float) ($get('item_price') ?? 0);
                                $ppu = $itemUnit > 0 ? ($itemPrice / $itemUnit) : 0;
                                return '₱' . number_format($ppu, 4) . ' / ' . ($get('unit') ?? 'unit');
                            }),
                        TextInput::make('stock_qty')
                            ->label('Total Current Stock Qty')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(0)
                            ->extraInputAttributes(['inputmode' => 'decimal'])
                            ->helperText('Total stock available in inventory (in base unit, e.g. grams)'),
                        TextInput::make('min_stock_qty')
                            ->label('Min Reorder Level')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->default(100)
                            ->extraInputAttributes(['inputmode' => 'decimal']),
                        TextInput::make('reorder_qty')
                            ->label('Reorder Batch Qty')
                            ->numeric()
                            ->minValue(0)
                            ->default(1000)
                            ->extraInputAttributes(['inputmode' => 'decimal']),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Supplier Name')
                                    ->required(),
                                TextInput::make('contact_person')
                                    ->label('Contact Person'),
                                TextInput::make('phone')
                                    ->label('Phone Number (11 Digits)')
                                    ->placeholder('09171234567')
                                    ->tel()
                                    ->numeric()
                                    ->length(11)
                                    ->regex('/^09\d{9}$/'),
                                TextInput::make('email')
                                    ->email()
                                    ->label('Email Address'),
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $sup = \App\Models\Supplier::find($state);
                                    if ($sup) {
                                        $set('supplier_name', $sup->name);
                                    }
                                }
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->searchable()->sortable()->weight('bold'),
                TextColumn::make('name')->label('Ingredient Name')->searchable()->sortable(),
                TextColumn::make('unit')->label('Unit')->sortable(),
                TextColumn::make('item_unit')
                    ->label('Item Unit')
                    ->numeric(3)
                    ->sortable(),
                TextColumn::make('item_price')
                    ->label('Item Price')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('cost_per_unit')
                    ->label('Price Unit (Cost/Unit)')
                    ->formatStateUsing(function ($state) {
                        $val = (float) $state;
                        if ($val == 0) return '₱0.00';
                        // Format with up to 6 decimals, then strip trailing zeros keeping min 2
                        $formatted = rtrim(number_format($val, 6), '0');
                        // Ensure at least 2 decimal places
                        $parts = explode('.', $formatted);
                        if (!isset($parts[1]) || strlen($parts[1]) < 2) {
                            $formatted = number_format($val, 2);
                        }
                        return '₱' . $formatted;
                    })
                    ->sortable()
                    ->weight('semibold')
                    ->color('primary'),
                TextColumn::make('stock_qty')
                    ->label('Total Stock Qty')
                    ->sortable()
                    ->formatStateUsing(fn($state, Ingredient $record) => number_format((float) $state, 2) . " {$record->unit}")
                    ->color(fn(Ingredient $record) => $record->is_low_stock ? 'danger' : 'success')
                    ->weight('bold'),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->default(fn(Ingredient $record) => $record->supplier_name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('low_stock')
                    ->label('Low Stock Alert Only')
                    ->query(fn($query) => $query->whereColumn('stock_qty', '<=', 'min_stock_qty')),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('quick_restock')
                    ->label('Restock 📦')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('add_qty')
                            ->label('Add Stock Quantity')
                            ->numeric()
                            ->minValue(0.01)
                            ->required()
                            ->suffix(fn(Ingredient $record) => $record->unit),
                    ])
                    ->action(function (Ingredient $record, array $data) {
                        $record->increment('stock_qty', $data['add_qty']);
                        Notification::make()
                            ->title("Added {$data['add_qty']} {$record->unit} to {$record->name} stock! 📦")
                            ->success()
                            ->send();
                    }),
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
