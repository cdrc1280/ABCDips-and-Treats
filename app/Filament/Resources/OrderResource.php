<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn as RepeatableTableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders & Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                /*
                |--------------------------------------------------------------------------
                | Customer & Order Details
                |--------------------------------------------------------------------------
                */
                Section::make('Customer & Order Details')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('order_number')
                            ->label('Order Number')
                            ->readOnly(),

                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->required(),

                        TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required(),

                        TextInput::make('customer_phone')
                            ->label('Phone')
                            ->required(),

                        Select::make('fulfillment_type')
                            ->label('Fulfillment')
                            ->options([
                                'delivery' => 'Delivery',
                                'pickup' => 'Store Pickup',
                            ])
                            ->required(),

                        TextInput::make('region')
                            ->label('Region')
                            ->readOnly(),

                        TextInput::make('province')
                            ->label('Province')
                            ->readOnly(),

                        TextInput::make('city')
                            ->label('City / Municipality')
                            ->readOnly(),

                        TextInput::make('barangay')
                            ->label('Barangay')
                            ->readOnly(),

                        TextInput::make('street_address')
                            ->label('Street / Landmark')
                            ->readOnly()
                            ->columnSpanFull(),

                        Textarea::make('delivery_address')
                            ->label('Full Combined Address')
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Customer Notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Order Items & Specifications
                |--------------------------------------------------------------------------
                |
                | CREATE:
                |   Use a normal Repeater so admins can add/edit items.
                |
                | EDIT:
                |   Use RepeatableEntry table so existing order items are displayed
                |   cleanly without looking like disabled form fields.
                |
                */
                Section::make('Order Items & Specifications')
                    ->icon('heroicon-o-shopping-bag')
                    ->description('Products, quantities, variations, and pricing included in this order.')
                    ->columnSpanFull()
                    ->components([

                        /*
                        |--------------------------------------------------------------------------
                        | CREATE ORDER
                        |--------------------------------------------------------------------------
                        */
                        Repeater::make('items')
                            ->relationship('items')
                            ->label('Items')
                            ->visible(fn (string $operation): bool => $operation === 'create')
                            ->table([
                                RepeaterTableColumn::make('Product')
                                    ->width('30%'),

                                RepeaterTableColumn::make('Flavor')
                                    ->width('18%'),

                                RepeaterTableColumn::make('Variation / Size')
                                    ->width('18%'),

                                RepeaterTableColumn::make('Qty')
                                    ->width('8%'),

                                RepeaterTableColumn::make('Unit Price')
                                    ->width('13%'),

                                RepeaterTableColumn::make('Subtotal')
                                    ->width('13%'),
                            ])
                            ->schema([
                                TextInput::make('product_name')
                                    ->label('Product')
                                    ->required()
                                    ->placeholder('Product name'),

                                TextInput::make('options.flavor')
                                    ->label('Flavor')
                                    ->placeholder('—'),

                                TextInput::make('options.variation')
                                    ->label('Variation / Size')
                                    ->placeholder('—'),

                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required(),

                                TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->required(),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->readOnly(),
                            ])
                            ->addActionLabel('Add Item')
                            ->reorderable(false)
                            ->collapsible(false)
                            ->cloneable(false)
                            ->columnSpanFull(),

                        /*
                        |--------------------------------------------------------------------------
                        | EDIT ORDER - READ ONLY TABLE
                        |--------------------------------------------------------------------------
                        */
                        RepeatableEntry::make('items')
                            ->label('Items breakdown')
                            ->visible(fn (string $operation): bool => $operation !== 'create')
                            ->table([
                                RepeatableTableColumn::make('Product')
                                    ->width('32%'),

                                RepeatableTableColumn::make('Flavor')
                                    ->width('17%'),

                                RepeatableTableColumn::make('Variation / Size')
                                    ->width('18%'),

                                RepeatableTableColumn::make('Qty')
                                    ->width('8%')
                                    ->alignment(\Filament\Support\Enums\Alignment::Center),

                                RepeatableTableColumn::make('Unit Price')
                                    ->width('12%')
                                    ->alignment(\Filament\Support\Enums\Alignment::End),

                                RepeatableTableColumn::make('Subtotal')
                                    ->width('13%')
                                    ->alignment(\Filament\Support\Enums\Alignment::End),
                            ])
                            ->schema([
                                TextEntry::make('product_name')
                                    ->label('Product')
                                    ->weight('bold')
                                    ->color('gray')
                                    ->formatStateUsing(
                                        fn ($state): string => $state ?: 'Unnamed Product'
                                    ),

                                TextEntry::make('options.flavor')
                                    ->label('Flavor')
                                    ->placeholder('—')
                                    ->formatStateUsing(
                                        fn ($state): string => $state ?: '—'
                                    ),

                                TextEntry::make('options.variation')
                                    ->label('Variation / Size')
                                    ->placeholder('—')
                                    ->formatStateUsing(
                                        fn ($state): string => $state ?: '—'
                                    ),

                                TextEntry::make('qty')
                                    ->label('Qty')
                                    ->weight('bold')
                                    ->alignCenter()
                                    ->formatStateUsing(
                                        fn ($state): string => number_format((int) $state)
                                    ),

                                TextEntry::make('unit_price')
                                    ->label('Unit Price')
                                    ->alignRight()
                                    ->formatStateUsing(
                                        fn ($state): string => '₱' . number_format((float) $state, 2)
                                    ),

                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->alignRight()
                                    ->weight('bold')
                                    ->color('primary')
                                    ->formatStateUsing(
                                        fn ($state): string => '₱' . number_format((float) $state, 2)
                                    ),
                            ])
                            ->contained(false)
                            ->columnSpanFull(),
                    ]),

                /*
                |--------------------------------------------------------------------------
                | Status & Payment
                |--------------------------------------------------------------------------
                */
                Section::make('Status & Payment')
                    ->columnSpanFull()
                    ->components([
                        Select::make('status')
                            ->label('Order Status')
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
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'awaiting_payment' => 'Awaiting Payment',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->required(),

                        TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->readOnly(),

                        TextInput::make('payment_reference')
                            ->label('Payment Reference'),

                        TextInput::make('total')
                            ->label('Order Total')
                            ->prefix('₱')
                            ->numeric()
                            ->readOnly(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                /*
                |--------------------------------------------------------------------------
                | Order Number
                |--------------------------------------------------------------------------
                */
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                /*
                |--------------------------------------------------------------------------
                | Customer
                |--------------------------------------------------------------------------
                */
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | Fulfillment
                |--------------------------------------------------------------------------
                */
                TextColumn::make('fulfillment_type')
                    ->label('Fulfillment')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'delivery' => 'Delivery',
                            'pickup' => 'Store Pickup',
                            default => ucwords(str_replace('_', ' ', $state)),
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'delivery' => 'info',
                            'pickup' => 'warning',
                            default => 'gray',
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | Delivery Mode
                |--------------------------------------------------------------------------
                */
                TextColumn::make('delivery_mode')
                    ->label('Delivery Mode')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state, Order $record): string => match ($state) {
                            Order::MODE_POOLING =>
                                '🤝 Pooling (' .
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $record->pooling_status ?? 'awaiting'
                                    )
                                ) .
                                ')',

                            default => '⚡ Priority',
                        }
                    )
                    ->color(
                        fn (string $state, Order $record): string => match ($state) {
                            Order::MODE_POOLING => match ($record->pooling_status) {
                                Order::POOLING_SETTLED => 'success',
                                Order::POOLING_POOLED => 'info',
                                default => 'warning',
                            },

                            default => 'gray',
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | Order Status
                |--------------------------------------------------------------------------
                */
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            Order::STATUS_PENDING => 'warning',
                            Order::STATUS_CONFIRMED => 'info',
                            Order::STATUS_PREPARING => 'primary',
                            Order::STATUS_PACKAGING => 'primary',
                            Order::STATUS_OUT_FOR_DELIVERY => 'info',
                            Order::STATUS_READY_FOR_PICKUP => 'info',
                            Order::STATUS_COMPLETED => 'success',
                            Order::STATUS_CANCELLED,
                            Order::STATUS_REFUNDED => 'danger',
                            default => 'gray',
                        }
                    )
                    ->formatStateUsing(
                        fn (string $state): string => ucwords(
                            str_replace('_', ' ', $state)
                        )
                    ),

                /*
                |--------------------------------------------------------------------------
                | Total
                |--------------------------------------------------------------------------
                */
                TextColumn::make('total')
                    ->label('Total')
                    ->money('PHP')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | Payment Method
                |--------------------------------------------------------------------------
                */
                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state): string => match (strtolower($state ?? '')) {
                            'qrph' => 'QR PH',
                            'gcash' => 'GCASH',
                            'maya' => 'MAYA',
                            'bank_transfer' => 'BDO BANK',
                            'cod' => 'COD',
                            default => strtoupper($state ?? ''),
                        }
                    )
                    ->color(
                        fn ($state): string => match (strtolower($state ?? '')) {
                            'qrph' => 'info',
                            'gcash' => 'primary',
                            'maya' => 'success',
                            'bank_transfer' => 'warning',
                            'cod' => 'gray',
                            default => 'gray',
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | Created
                |--------------------------------------------------------------------------
                */
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                SelectFilter::make('status')
                    ->label('Order Status')
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
                    ->label('Payment Method')
                    ->options([
                        'gcash' => 'GCash E-Wallet',
                        'maya' => 'Maya Wallet / Card',
                        'qrph' => 'QR Ph (Any Bank / E-Wallet)',
                        'bank_transfer' => 'BDO Bank Transfer',
                    ]),

                SelectFilter::make('fulfillment_type')
                    ->label('Fulfillment')
                    ->options([
                        'delivery' => 'Delivery',
                        'pickup' => 'Pickup',
                    ]),

                SelectFilter::make('delivery_mode')
                    ->label('Delivery Mode')
                    ->options([
                        Order::MODE_PRIORITY => '⚡ Priority Express',
                        Order::MODE_POOLING => '🤝 Delivery Pooling (Shared)',
                    ]),
            ])

            ->actions([
                /*
                |--------------------------------------------------------------------------
                | Advance Status
                |--------------------------------------------------------------------------
                */
                Action::make('advance_status')
                    ->label('Advance Status')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->action(function (Order $record) {
                        /*
                        |--------------------------------------------------------------------------
                        | Pooling Protection
                        |--------------------------------------------------------------------------
                        |
                        | A pooling order cannot move forward until the admin has
                        | assigned and settled the pooled shipping fee.
                        |
                        */
                        if (
                            $record->delivery_mode === Order::MODE_POOLING &&
                            $record->pooling_status !== Order::POOLING_SETTLED
                        ) {
                            Notification::make()
                                ->title('🚫 Pooling Rate Settlement Required')
                                ->body(
                                    "Order #{$record->order_number} is a Delivery Pooling order awaiting admin assignment. " .
                                    "You MUST assign this order to a Delivery Pool Batch and settle the shared shipping fee in 'Delivery Pooling' before advancing status."
                                )
                                ->danger()
                                ->persistent()
                                ->send();

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Determine Next Status
                        |--------------------------------------------------------------------------
                        */
                        $nextStatus = match ($record->status) {
                            Order::STATUS_PENDING =>
                                Order::STATUS_CONFIRMED,

                            Order::STATUS_CONFIRMED =>
                                Order::STATUS_PREPARING,

                            Order::STATUS_PREPARING =>
                                Order::STATUS_PACKAGING,

                            Order::STATUS_PACKAGING =>
                                $record->fulfillment_type === 'delivery'
                                    ? Order::STATUS_OUT_FOR_DELIVERY
                                    : Order::STATUS_READY_FOR_PICKUP,

                            Order::STATUS_OUT_FOR_DELIVERY,
                            Order::STATUS_READY_FOR_PICKUP =>
                                Order::STATUS_COMPLETED,

                            default =>
                                $record->status,
                        };

                        if (
                            $record->delivery_mode === Order::MODE_POOLING &&
                            $record->payment_status !== 'paid' &&
                            in_array($nextStatus, [Order::STATUS_PREPARING, Order::STATUS_PACKAGING, Order::STATUS_OUT_FOR_DELIVERY, Order::STATUS_COMPLETED])
                        ) {
                            Notification::make()
                                ->title('💳 Customer Payment Required')
                                ->body(
                                    "Order #{$record->order_number} uses Group Delivery Pooling. The customer MUST settle their payment (₱" . number_format($record->total, 2) . ") before kitchen baking & processing can begin."
                                )
                                ->danger()
                                ->persistent()
                                ->send();

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Apply Transition
                        |--------------------------------------------------------------------------
                        */
                        if ($nextStatus !== $record->status) {
                            try {
                                $record->transitionTo(
                                    $nextStatus,
                                    'Status advanced by admin.'
                                );
                            } catch (\DomainException $e) {
                                Notification::make()
                                    ->title('🚫 Status Transition Blocked')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                                return;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Mark Order Notifications As Read
                            |--------------------------------------------------------------------------
                            */
                            DB::table('notifications')
                                ->where(
                                    'data',
                                    'like',
                                    "%{$record->order_number}%"
                                )
                                ->whereNull('read_at')
                                ->update([
                                    'read_at' => now(),
                                ]);

                            Notification::make()
                                ->title(
                                    'Order status updated to ' .
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $nextStatus
                                        )
                                    )
                                )
                                ->success()
                                ->send();
                        }
                    }),

                /*
                |--------------------------------------------------------------------------
                | Invoice
                |--------------------------------------------------------------------------
                */
                Action::make('download_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->url(
                        fn (?Order $record = null): string =>
                            $record
                                ? url("/order-invoice/{$record->id}")
                                : '#'
                    )
                    ->openUrlInNewTab(),

                ViewAction::make(),

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
