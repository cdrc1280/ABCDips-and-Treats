<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryPoolResource\Pages\CreateDeliveryPool;
use App\Filament\Resources\DeliveryPoolResource\Pages\EditDeliveryPool;
use App\Filament\Resources\DeliveryPoolResource\Pages\ListDeliveryPools;
use App\Models\DeliveryPool;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class DeliveryPoolResource extends Resource
{
    protected static ?string $model = DeliveryPool::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders & Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Delivery Pool Batch';

    protected static ?string $pluralLabel = 'Delivery Pooling (Shared Shipping)';

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = Order::where('delivery_mode', Order::MODE_POOLING)
            ->whereIn('pooling_status', [Order::POOLING_AWAITING_ASSIGNMENT, Order::POOLING_POOLED])
            ->count();

        return $pendingCount ? "{$pendingCount} Pending" : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('📋 Unassigned Customer Orders Awaiting Delivery Pooling')
                    ->description('These customer orders chose Group Delivery Pooling at checkout and are currently waiting for admin pool batch assignment.')
                    ->columnSpanFull()
                    ->components([
                        ViewField::make('pending_pooled_orders_list')
                            ->view('filament.forms.components.pending-pooled-orders-list'),
                    ]),

                Section::make('Delivery Pool Details')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('pool_code')
                            ->label('Pool Batch Code (Auto)')
                            ->default(fn() => 'POOL-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                            ->required()
                            ->readOnly()
                            ->unique(DeliveryPool::class, 'pool_code', ignoreRecord: true),

                        Select::make('region')
                            ->label('Batch Region')
                            ->options(fn () => \App\Services\PsgcService::getRegionsOptions())
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('province', null);
                                $set('city', null);
                                $set('barangay', null);
                            }),

                        Select::make('province')
                            ->label('Batch Province')
                            ->options(fn (callable $get) => \App\Services\PsgcService::getProvincesOptions($get('region')))
                            ->searchable()
                            ->live()
                            ->disabled(fn (callable $get) => !$get('region') || $get('region') === '130000000')
                            ->afterStateUpdated(function (callable $set) {
                                $set('city', null);
                                $set('barangay', null);
                            }),

                        Select::make('city')
                            ->label('Target City / District')
                            ->options(fn (callable $get) => \App\Services\PsgcService::getCitiesOptions($get('region'), $get('province')))
                            ->searchable()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (callable $set) {
                                $set('barangay', null);
                            }),

                        Select::make('barangay')
                            ->label('Batch Barangay')
                            ->options(fn (callable $get) => \App\Services\PsgcService::getBarangaysOptions($get('city')))
                            ->searchable()
                            ->live(),

                        TextInput::make('zone_name')
                            ->label('Zone / Street Landmark')
                            ->placeholder('e.g. Molino III / Zapote Corridor')
                            ->required(),

                        Select::make('status')
                            ->options([
                                DeliveryPool::STATUS_OPEN       => 'Open (Building Pool)',
                                DeliveryPool::STATUS_BUILDING   => 'Building / Grouping',
                                DeliveryPool::STATUS_SETTLED    => 'Settled (Rates Split & Approved)',
                                DeliveryPool::STATUS_DISPATCHED => 'Out for Shared Delivery',
                            ])
                            ->required()
                            ->default(DeliveryPool::STATUS_OPEN),

                        TextInput::make('total_delivery_fee')
                            ->label('Actual Combined Lalamove Route Fee (₱)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->required()
                            ->default(150.00)
                            ->live(),

                        Textarea::make('notes')
                            ->label('Driver & Delivery Instructions')
                            ->placeholder('e.g. Lalamove Rider: Juan Dela Cruz (0917-000-1111) — Drop off order 1 first, then order 2.')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('🤝 Select & Assign Customer Orders One-by-One')
                    ->description('Select pending customer orders for this batch and assign custom shipping fees. The remaining route balance auto-deducts in real time above.')
                    ->columnSpanFull()
                    ->components([
                        ViewField::make('financial_summary')
                            ->view('filament.forms.components.delivery-pool-financial-summary')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Repeater::make('order_allocations')
                            ->label('Customer Order Allocations')
                            ->columnSpanFull()
                            ->columns(2)
                            ->live()
                            ->schema([
                                Select::make('order_id')
                                    ->label('Select Customer Order')
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->options(function (?DeliveryPool $record = null) {
                                        return Order::where(function ($q) {
                                                $q->where('delivery_mode', Order::MODE_POOLING)
                                                  ->orWhere('pooling_status', Order::POOLING_AWAITING_ASSIGNMENT)
                                                  ->orWhere('pooling_status', Order::POOLING_POOLED);
                                            })
                                            ->where(function ($q) use ($record) {
                                                $q->whereNull('delivery_pool_id');
                                                if ($record) {
                                                    $q->orWhere('delivery_pool_id', $record->id);
                                                }
                                            })
                                            ->get()
                                            ->mapWithKeys(function (Order $o) {
                                                $addrParts = array_filter([$o->street_address, $o->barangay, $o->city, $o->province, $o->region]);
                                                $addrStr = !empty($addrParts) ? implode(', ', $addrParts) : ($o->delivery_address ?: 'N/A');
                                                return [
                                                    $o->id => "#{$o->order_number} — {$o->customer_name} ({$addrStr}) [Subtotal: \u{20B1}" . number_format($o->subtotal, 2) . "]"
                                                ];
                                            });
                                    })
                                    ->required()
                                    ->live(),

                                TextInput::make('assigned_fee')
                                    ->label('Assigned Customer Shipping Fee (₱)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('₱')
                                    ->required()
                                    ->default(0.00)
                                    ->live(),
                            ]),
                    ]),

                Section::make('Assigned Customer Orders Summary')
                    ->columnSpanFull()
                    ->components([
                        ViewField::make('assigned_pooled_orders_summary')
                            ->view('filament.forms.components.assigned-pooled-orders-summary'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pool_code')
                    ->label('Batch Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('city')
                    ->label('City / Zone')
                    ->searchable()
                    ->badge(),

                TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Pooled Customers')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('total_delivery_fee')
                    ->label('Actual Combined Cost')
                    ->money('PHP')
                    ->sortable(),

                TextColumn::make('shared_fee_per_order')
                    ->label('Shared Fee / Customer')
                    ->money('PHP')
                    ->weight('black')
                    ->badge()
                    ->color('success'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        DeliveryPool::STATUS_OPEN => 'warning',
                        DeliveryPool::STATUS_BUILDING => 'info',
                        DeliveryPool::STATUS_SETTLED => 'success',
                        DeliveryPool::STATUS_DISPATCHED => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => ucfirst($state)),

                TextColumn::make('settled_at')
                    ->label('Settled Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        DeliveryPool::STATUS_OPEN       => 'Open',
                        DeliveryPool::STATUS_BUILDING   => 'Building',
                        DeliveryPool::STATUS_SETTLED    => 'Settled',
                        DeliveryPool::STATUS_DISPATCHED => 'Dispatched',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('settle_and_dispatch')
                    ->label('Settle & Dispatch Pool 🤝')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->modalHeading('Settle Delivery Pooling Batch')
                    ->modalDescription('Specify individual shared delivery fee for each customer in this pool batch based on their distance and border location.')
                    ->visible(fn(DeliveryPool $record) => $record->status !== DeliveryPool::STATUS_SETTLED && $record->status !== DeliveryPool::STATUS_DISPATCHED)
                    ->form(function (DeliveryPool $record) {
                        $record->loadMissing('orders');
                        $defaultShared = round($record->total_delivery_fee / max(1, $record->orders->count()), 2);
                        $fields = [];

                        foreach ($record->orders as $order) {
                            $fields[] = TextInput::make("fee_for_order_{$order->id}")
                                ->label("Shipping Fee: #{$order->order_number} — {$order->customer_name} ({$order->city})")
                                ->hint("Address: {$order->delivery_address}")
                                ->numeric()
                                ->minValue(0)
                                ->prefix('₱')
                                ->default($order->delivery_fee > 0 ? $order->delivery_fee : $defaultShared)
                                ->required();
                        }

                        return $fields;
                    })
                    ->action(function (DeliveryPool $record, array $data) {
                        $record->loadMissing('orders');

                        if ($record->orders->isEmpty()) {
                            Notification::make()
                                ->title('Cannot Settle Empty Pool')
                                ->body('Please assign at least one pending pooled order to this batch.')
                                ->warning()
                                ->send();
                            return;
                        }

                        $sumFees = 0;
                        foreach ($record->orders as $order) {
                            $customFee = (float) ($data["fee_for_order_{$order->id}"] ?? 0.00);
                            $sumFees += $customFee;
                            $oldTotal  = $order->total;
                            $newTotal  = max(0.0, round($order->subtotal - $order->discount_amount + $customFee, 2));

                            $order->update([
                                'delivery_fee'   => $customFee,
                                'total'          => $newTotal,
                                'pooling_status' => Order::POOLING_SETTLED,
                                'status'         => Order::STATUS_CONFIRMED,
                            ]);

                            $order->statusHistories()->create([
                                'from_status' => Order::STATUS_PENDING,
                                'to_status'   => Order::STATUS_CONFIRMED,
                                'comment'     => "Delivery Pool #{$record->pool_code} settled by Admin. Custom shared shipping fee assigned: \u{20B1}" . number_format($customFee, 2) . " (previous total: \u{20B1}" . number_format($oldTotal, 2) . ", new total: \u{20B1}" . number_format($newTotal, 2) . ").",
                            ]);
                        }

                        $count = $record->orders->count();
                        $avgFee = round($sumFees / max(1, $count), 2);

                        $record->update([
                            'shared_fee_per_order' => $avgFee,
                            'status'               => DeliveryPool::STATUS_SETTLED,
                            'settled_at'           => now(),
                        ]);

                        Notification::make()
                            ->title("Delivery Pool Batch #{$record->pool_code} Settled! 🤝")
                            ->body("Custom individual delivery fees assigned and saved for {$count} customer orders.")
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
            'index'  => ListDeliveryPools::route('/'),
            'create' => CreateDeliveryPool::route('/create'),
            'edit'   => EditDeliveryPool::route('/{record}/edit'),
        ];
    }
}
