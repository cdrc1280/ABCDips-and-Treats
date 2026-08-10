<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages\CreatePurchaseOrder;
use App\Filament\Resources\PurchaseOrderResource\Pages\EditPurchaseOrder;
use App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use App\Models\PurchaseOrder;
use App\Services\PurchasingService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'Production & Purchasing';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Purchase Order Details')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('po_number')
                            ->label('PO Number (Auto-generated)')
                            ->default(fn() => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                            ->readOnly()
                            ->required(),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options([
                                PurchaseOrder::STATUS_DRAFT => 'Draft',
                                PurchaseOrder::STATUS_SENT => 'Sent to Supplier',
                                PurchaseOrder::STATUS_RECEIVED => 'Received & Restocked',
                                PurchaseOrder::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->default(PurchaseOrder::STATUS_DRAFT)
                            ->required(),
                        DatePicker::make('expected_delivery_date')
                            ->label('Expected Delivery Date'),
                        TextInput::make('subtotal')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),
                        TextInput::make('tax')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),
                        TextInput::make('total')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),
                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(2),

                Section::make('Ordered Line Items')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('items')
                            ->relationship('items')
                            ->components([
                                Select::make('ingredient_id')
                                    ->label('Select Ingredient')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $ing = \App\Models\Ingredient::find($state);
                                            if ($ing) {
                                                $set('unit_cost', $ing->item_price);
                                            }
                                        }
                                    }),
                                TextInput::make('qty_ordered')
                                    ->label('Qty Ordered')
                                    ->numeric()
                                    ->minValue(0.001)
                                    ->required()
                                    ->default(1)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('unit_cost')
                                    ->label('Package Unit Cost (₱)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('₱')
                                    ->required()
                                    ->default(0)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
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
                TextColumn::make('po_number')->searchable()->sortable()->weight('bold'),
                TextColumn::make('supplier.name')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        PurchaseOrder::STATUS_DRAFT => 'warning',
                        PurchaseOrder::STATUS_SENT => 'info',
                        PurchaseOrder::STATUS_RECEIVED => 'success',
                        PurchaseOrder::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('total')->money('PHP')->sortable(),
                TextColumn::make('expected_delivery_date')->date(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                Action::make('receive_po')
                    ->label('Receive PO & Restock')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn(PurchaseOrder $record) => $record->status !== PurchaseOrder::STATUS_RECEIVED)
                    ->action(function (PurchaseOrder $record) {
                        $purchasingService = app(PurchasingService::class);
                        $purchasingService->receivePurchaseOrder($record);

                        Notification::make()
                            ->title("PO {$record->po_number} received!")
                            ->body("Restocked all line items into raw ingredients inventory.")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
