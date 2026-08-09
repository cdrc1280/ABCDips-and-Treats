<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomOrderResource\Pages\CreateCustomOrder;
use App\Filament\Resources\CustomOrderResource\Pages\EditCustomOrder;
use App\Filament\Resources\CustomOrderResource\Pages\ListCustomOrders;
use App\Models\CustomOrder;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CustomOrderResource extends Resource
{
    protected static ?string $model = CustomOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'Store';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer & Event Details')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('reference_number')->readOnly(),
                        TextInput::make('customer_name')->required(),
                        TextInput::make('customer_email')->email()->required(),
                        TextInput::make('customer_phone')->required(),
                        DatePicker::make('event_date')->required(),
                        TextInput::make('servings_count')->numeric()->required(),
                        TextInput::make('tiers_count')->numeric()->required(),
                        TextInput::make('flavor_preference'),
                        TextInput::make('frosting_type'),
                        Textarea::make('theme_description')->required()->columnSpanFull(),
                    ])->columns(2),

                Section::make('Reference Photos')
                    ->columnSpanFull()
                    ->components([
                        FileUpload::make('reference_photos')
                            ->label('Reference Photos')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->multiple()
                            ->disk('public')
                            ->directory('custom_orders')
                            ->visibility('public'),
                    ]),

                Section::make('Quote & Pipeline Status')
                    ->columnSpanFull()
                    ->components([
                        Select::make('status')
                            ->options([
                                CustomOrder::STATUS_INQUIRY => 'Inquiry Received',
                                CustomOrder::STATUS_QUOTED => 'Quote Sent',
                                CustomOrder::STATUS_DEPOSIT_PAID => 'Deposit Paid',
                                CustomOrder::STATUS_IN_PRODUCTION => 'In Production',
                                CustomOrder::STATUS_READY => 'Ready for Pickup/Delivery',
                                CustomOrder::STATUS_COMPLETED => 'Completed',
                                CustomOrder::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->required(),

                        TextInput::make('quoted_price')
                            ->label('Quoted Price (₱)')
                            ->numeric()
                            ->prefix('₱'),

                        Textarea::make('staff_notes')
                            ->label('Internal Staff Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('customer_name')
                    ->searchable(),

                TextColumn::make('event_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('servings_count')
                    ->label('Servings'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        CustomOrder::STATUS_INQUIRY => 'warning',
                        CustomOrder::STATUS_QUOTED => 'info',
                        CustomOrder::STATUS_DEPOSIT_PAID => 'success',
                        CustomOrder::STATUS_IN_PRODUCTION => 'primary',
                        CustomOrder::STATUS_READY => 'info',
                        CustomOrder::STATUS_COMPLETED => 'success',
                        CustomOrder::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => ucwords(str_replace('_', ' ', $state))),

                TextColumn::make('quoted_price')
                    ->money('PHP')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        CustomOrder::STATUS_INQUIRY => 'Inquiry Received',
                        CustomOrder::STATUS_QUOTED => 'Quoted',
                        CustomOrder::STATUS_DEPOSIT_PAID => 'Deposit Paid',
                        CustomOrder::STATUS_IN_PRODUCTION => 'In Production',
                        CustomOrder::STATUS_COMPLETED => 'Completed',
                    ]),
            ])
            ->actions([
                Action::make('mark_production')
                    ->label('Production 👨‍🍳')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn(CustomOrder $record) => in_array($record->status, [CustomOrder::STATUS_INQUIRY, CustomOrder::STATUS_QUOTED, CustomOrder::STATUS_DEPOSIT_PAID]))
                    ->action(function (CustomOrder $record) {
                        $record->update(['status' => CustomOrder::STATUS_IN_PRODUCTION]);
                        Notification::make()
                            ->title('Custom Order Moved to Production 👨‍🍳')
                            ->warning()
                            ->send();
                    }),
                Action::make('mark_completed')
                    ->label('Completed ✅')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(CustomOrder $record) => $record->status !== CustomOrder::STATUS_COMPLETED)
                    ->action(function (CustomOrder $record) {
                        $record->update(['status' => CustomOrder::STATUS_COMPLETED]);
                        Notification::make()
                            ->title('Custom Order Marked as Completed ✅')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomOrders::route('/'),
            'create' => CreateCustomOrder::route('/create'),
            'edit' => EditCustomOrder::route('/{record}/edit'),
        ];
    }
}
