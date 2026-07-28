<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages\CreatePurchaseOrder;
use App\Filament\Resources\PurchaseOrderResource\Pages\EditPurchaseOrder;
use App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use App\Models\PurchaseOrder;
use App\Services\PurchasingService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'Purchasing';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Purchase Order Details')
                    ->components([
                        TextInput::make('po_number')->readOnly(),
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->required(),
                        Select::make('status')
                            ->options([
                                PurchaseOrder::STATUS_DRAFT => 'Draft',
                                PurchaseOrder::STATUS_SENT => 'Sent to Supplier',
                                PurchaseOrder::STATUS_RECEIVED => 'Received & Restocked',
                                PurchaseOrder::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->required(),
                        DatePicker::make('expected_delivery_date'),
                        TextInput::make('subtotal')->numeric()->prefix('₱'),
                        TextInput::make('tax')->numeric()->prefix('₱'),
                        TextInput::make('total')->numeric()->prefix('₱'),
                        Textarea::make('notes')->columnSpanFull(),
                    ]),

                Section::make('Ordered Line Items')
                    ->components([
                        Repeater::make('items')
                            ->relationship()
                            ->components([
                                Select::make('ingredient_id')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable(),
                                TextInput::make('qty_ordered')->numeric()->required(),
                                TextInput::make('unit_cost')->numeric()->prefix('₱')->required(),
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
            ->actions([
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
