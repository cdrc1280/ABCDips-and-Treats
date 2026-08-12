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
                        Placeholder::make('pending_pooled_orders_list')
                            ->label('')
                            ->content(function () {
                                $pending = Order::where(function ($q) {
                                        $q->where('delivery_mode', Order::MODE_POOLING)
                                          ->orWhere('pooling_status', Order::POOLING_AWAITING_ASSIGNMENT);
                                    })
                                    ->whereNull('delivery_pool_id')
                                    ->get();

                                if ($pending->isEmpty()) {
                                    return new HtmlString('<div style="padding:12px; border-radius:8px; background-color:#f9fafb; border:1px solid #e5e7eb; color:#6b7280; font-size:13px; font-style:italic;">✨ All pooled orders have been assigned to delivery batches! No pending orders.</div>');
                                }

                                $html = '<div style="overflow-x:auto; margin-top:8px;"><table style="width:100%; text-align:left; border-collapse:collapse; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">';
                                $html .= '<thead style="background-color:#fffbeb; color:#92400e; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.05em;"><tr>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #fde68a;">Order #</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #fde68a;">Customer</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #fde68a;">Phone</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #fde68a;">City / District</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #fde68a;">Delivery Address</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #fde68a; text-align:right;">Items Subtotal</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #fde68a; text-align:center;">Pooling Status</th>';
                                $html .= '</tr></thead><tbody>';

                                foreach ($pending as $o) {
                                    $html .= "<tr style='border-bottom:1px solid #f3f4f6;'>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-weight:bold; font-family:monospace; color:#d97706;'>#{$o->order_number}</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-weight:600; color:#111827;'>{$o->customer_name}</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-family:monospace; font-size:12px; color:#4b5563;'>{$o->customer_phone}</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb;'><span style='padding:3px 8px; border-radius:6px; background-color:#fef3c7; color:#92400e; font-weight:700; font-size:11px;'>{$o->city}</span></td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-size:12px; color:#374151;'>{$o->delivery_address}</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; text-align:right; font-weight:bold; font-family:monospace; color:#b45309;'>₱" . number_format($o->subtotal, 2) . "</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; text-align:center;'><span style='padding:4px 10px; border-radius:12px; background-color:#d97706; color:#ffffff; font-weight:800; font-size:10px; text-transform:uppercase;'>⏳ Awaiting Batch</span></td>";
                                    $html .= "</tr>";
                                }

                                $html .= '</tbody></table></div>';
                                return new HtmlString($html);
                            }),
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

                        TextInput::make('city')
                            ->label('Target City / District')
                            ->placeholder('e.g. Bacoor / Imus, Cavite')
                            ->required(),

                        TextInput::make('zone_name')
                            ->label('Zone / Neighborhood Landmark')
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
                            ->live()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $totalCost = (float) ($get('total_delivery_fee') ?? 0);
                                $orderIds  = $get('assigned_orders') ?? [];
                                $count     = max(1, count($orderIds));
                                $set('shared_fee_per_order', round($totalCost / $count, 2));
                            }),

                        TextInput::make('shared_fee_per_order')
                            ->label('Shared Fee Per Customer Order (₱)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->required()
                            ->readOnly()
                            ->hint('Auto-calculated average fee (admin can assign custom rates per order during settlement)'),

                        Select::make('assigned_orders')
                            ->label('Assign Pending Pooled Orders to this Batch')
                            ->multiple()
                            ->searchable()
                            ->preload()
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
                                    ->mapWithKeys(fn(Order $o) => [
                                        $o->id => "#{$o->order_number} — {$o->customer_name} ({$o->city}, {$o->delivery_address}) [Items: ₱" . number_format($o->subtotal, 2) . "]"
                                    ]);
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $totalCost = (float) ($get('total_delivery_fee') ?? 0);
                                $count     = max(1, count($state ?? []));
                                $set('shared_fee_per_order', round($totalCost / $count, 2));
                            }),

                        Textarea::make('notes')
                            ->label('Driver & Delivery Instructions')
                            ->placeholder('e.g. Lalamove Rider: Juan Dela Cruz (0917-000-1111) — Drop off order 1 first, then order 2.')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Assigned Customer Orders Summary')
                    ->columnSpanFull()
                    ->components([
                        Placeholder::make('orders_table')
                            ->label('')
                            ->content(function (?DeliveryPool $record = null) {
                                if (!$record || $record->orders->isEmpty()) {
                                    return new HtmlString('<div style="padding:12px; border-radius:8px; background-color:#f9fafb; border:1px solid #e5e7eb; color:#6b7280; font-size:13px; font-style:italic; text-align:center;">No orders currently assigned to this delivery pool.</div>');
                                }
                                $html = '<div style="overflow-x:auto; margin-top:8px;"><table style="width:100%; text-align:left; border-collapse:collapse; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">';
                                $html .= '<thead style="background-color:#f3f4f6; color:#374151; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:0.05em;"><tr>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #e5e7eb;">Order #</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #e5e7eb;">Customer</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #e5e7eb;">Phone</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #e5e7eb;">Address</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #e5e7eb; text-align:right;">Items Subtotal</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #e5e7eb; text-align:right;">Custom Shipping Fee</th>';
                                $html .= '<th style="padding:10px 14px; border:1px solid #e5e7eb; text-align:right;">Final Total</th>';
                                $html .= '</tr></thead><tbody>';
                                foreach ($record->orders as $o) {
                                    $html .= "<tr style='border-bottom:1px solid #f3f4f6;'>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-weight:bold; font-family:monospace;'>#{$o->order_number}</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-weight:600;'>{$o->customer_name}</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-family:monospace; font-size:12px; color:#4b5563;'>{$o->customer_phone}</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; font-size:12px; color:#374151;'>{$o->delivery_address} ({$o->city})</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; text-align:right; font-family:monospace;'>₱" . number_format($o->subtotal, 2) . "</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; text-align:right; font-family:monospace; font-weight:bold; color:#059669;'>₱" . number_format($o->delivery_fee, 2) . "</td>";
                                    $html .= "<td style='padding:10px 14px; border:1px solid #e5e7eb; text-align:right; font-family:monospace; font-weight:900; color:#b45309;'>₱" . number_format($o->total, 2) . "</td>";
                                    $html .= "</tr>";
                                }
                                $html .= '</tbody></table></div>';
                                return new HtmlString($html);
                            }),
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
                                'comment'     => "Delivery Pool #{$record->pool_code} settled by Admin. Custom shared shipping fee assigned: ₱" . number_format($customFee, 2) . " (previous total: ₱" . number_format($oldTotal, 2) . ", new total: ₱" . number_format($newTotal, 2) . ").",
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
