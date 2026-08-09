<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'Store';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer & Order Details')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('order_number')->readOnly(),
                        TextInput::make('customer_name')->required(),
                        TextInput::make('customer_email')->email()->required(),
                        TextInput::make('customer_phone')->required(),
                        Select::make('fulfillment_type')
                            ->options(['delivery' => 'Delivery', 'pickup' => 'Store Pickup'])
                            ->required(),
                        Textarea::make('delivery_address')->columnSpanFull(),
                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(2),

                Section::make('Status & Payment')
                    ->columnSpanFull()
                    ->components([
                        Select::make('status')
                            ->options([
                                Order::STATUS_PENDING => 'Pending',
                                Order::STATUS_CONFIRMED => 'Confirmed',
                                Order::STATUS_PREPARING => 'Preparing in Kitchen',
                                Order::STATUS_PACKAGING => 'Packaging',
                                Order::STATUS_OUT_FOR_DELIVERY => 'Out for Delivery',
                                Order::STATUS_READY_FOR_PICKUP => 'Ready for Pickup',
                                Order::STATUS_COMPLETED => 'Completed',
                                Order::STATUS_CANCELLED => 'Cancelled',
                                Order::STATUS_REFUNDED => 'Refunded',
                                Order::STATUS_ARCHIVED => 'Archived',
                            ])
                            ->required(),

                        Select::make('payment_status')
                            ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'])
                            ->required(),

                        TextInput::make('payment_method')->readOnly(),
                        TextInput::make('payment_reference'),
                        TextInput::make('total')->prefix('₱')->numeric()->readOnly(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fulfillment_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'delivery' => 'info',
                        'pickup' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Order::STATUS_PENDING => 'warning',
                        Order::STATUS_CONFIRMED => 'info',
                        Order::STATUS_PREPARING => 'primary',
                        Order::STATUS_PACKAGING => 'primary',
                        Order::STATUS_OUT_FOR_DELIVERY => 'info',
                        Order::STATUS_READY_FOR_PICKUP => 'info',
                        Order::STATUS_COMPLETED => 'success',
                        Order::STATUS_CANCELLED, Order::STATUS_REFUNDED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucwords(str_replace('_', ' ', $state))),

                TextColumn::make('total')
                    ->money('PHP')
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->badge()
                    ->formatStateUsing(fn($state) => match(strtolower($state ?? '')) {
                        'qrph'          => 'QR PH',
                        'gcash'         => 'GCASH',
                        'maya'          => 'MAYA',
                        'bank_transfer' => 'BDO BANK',
                        'cod'           => 'COD',
                        default         => strtoupper($state ?? ''),
                    })
                    ->color(fn($state) => match(strtolower($state ?? '')) {
                        'qrph'          => 'info',
                        'gcash'         => 'primary',
                        'maya'          => 'success',
                        'bank_transfer' => 'warning',
                        'cod'           => 'gray',
                        default         => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Order::STATUS_PENDING => 'Pending',
                        Order::STATUS_CONFIRMED => 'Confirmed',
                        Order::STATUS_PREPARING => 'Preparing',
                        Order::STATUS_PACKAGING => 'Packaging',
                        Order::STATUS_OUT_FOR_DELIVERY => 'Out for Delivery',
                        Order::STATUS_READY_FOR_PICKUP => 'Ready for Pickup',
                        Order::STATUS_COMPLETED => 'Completed',
                        Order::STATUS_CANCELLED => 'Cancelled',
                    ]),

                SelectFilter::make('payment_method')
                    ->options([
                        'gcash'         => 'GCash E-Wallet',
                        'maya'          => 'Maya Wallet / Card',
                        'qrph'          => 'QR Ph (Any Bank)',
                        'bank_transfer' => 'BDO Bank Transfer',
                        'cod'           => 'Cash on Delivery (COD)',
                    ]),

                SelectFilter::make('fulfillment_type')
                    ->options(['delivery' => 'Delivery', 'pickup' => 'Pickup']),
            ])
            ->actions([
                Action::make('advance_status')
                    ->label('Advance Status')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->action(function (Order $record) {
                        $nextStatus = match ($record->status) {
                            Order::STATUS_PENDING => Order::STATUS_CONFIRMED,
                            Order::STATUS_CONFIRMED => Order::STATUS_PREPARING,
                            Order::STATUS_PREPARING => Order::STATUS_PACKAGING,
                            Order::STATUS_PACKAGING => $record->fulfillment_type === 'delivery' ? Order::STATUS_OUT_FOR_DELIVERY : Order::STATUS_READY_FOR_PICKUP,
                            Order::STATUS_OUT_FOR_DELIVERY, Order::STATUS_READY_FOR_PICKUP => Order::STATUS_COMPLETED,
                            default => $record->status,
                        };

                        if ($nextStatus !== $record->status) {
                            $record->transitionTo($nextStatus, 'Status advanced by admin.');

                            // Auto mark database notification for this order as read
                            \Illuminate\Support\Facades\DB::table('notifications')
                                ->where('data', 'like', "%{$record->order_number}%")
                                ->whereNull('read_at')
                                ->update(['read_at' => now()]);

                            Notification::make()
                                ->title("Order status updated to " . ucwords(str_replace('_', ' ', $nextStatus)))
                                ->success()
                                ->send();
                        }
                    }),
                Action::make('download_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(fn (Order $record) => url("/order-invoice/{$record->id}"))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
