<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages\CreateCoupon;
use App\Filament\Resources\CouponResource\Pages\EditCoupon;
use App\Filament\Resources\CouponResource\Pages\ListCoupons;
use App\Models\Coupon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';
    protected static string|\UnitEnum|null $navigationGroup = 'Store';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Coupon Details')->components([
                TextInput::make('code')
                    ->label('Coupon Code (Auto-generated)')
                    ->default(fn() => 'ABCD-' . strtoupper(Str::random(6)))
                    ->readOnly()
                    ->required()
                    ->unique(Coupon::class, 'code', ignoreRecord: true),
                Select::make('type')
                    ->options(['fixed' => 'Fixed Amount (₱)', 'percent' => 'Percentage (%)'])
                    ->required(),
                TextInput::make('value')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->prefix('₱/%')
                    ->extraInputAttributes(['inputmode' => 'decimal']),
                TextInput::make('min_spend')
                    ->label('Minimum Order (₱)')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('₱')
                    ->extraInputAttributes(['inputmode' => 'decimal']),
                TextInput::make('max_uses')
                    ->label('Max Uses')
                    ->integer()
                    ->minValue(1)
                    ->extraInputAttributes(['inputmode' => 'numeric']),
                TextInput::make('used_count')->label('Times Used')->numeric()->disabled(),
                DateTimePicker::make('expires_at'),
                Toggle::make('is_active')->default(true),
            ])->columnSpanFull()->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->weight('bold'),
                TextColumn::make('type')->badge(),
                TextColumn::make('value')->prefix('₱')->sortable(),
                TextColumn::make('used_count')->label('Used')->sortable(),
                TextColumn::make('max_uses')->label('Max'),
                TextColumn::make('expires_at')->date(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ActionGroup::make([
                    Action::make('toggle_active')
                        ->label(fn(Coupon $record) => $record->is_active ? 'Deactivate Coupon' : 'Activate Coupon 🎟️')
                        ->icon('heroicon-o-power')
                        ->color(fn(Coupon $record) => $record->is_active ? 'danger' : 'success')
                        ->action(function (Coupon $record) {
                            $record->update(['is_active' => !$record->is_active]);
                            Notification::make()
                                ->title($record->is_active ? 'Coupon Activated 🎟️' : 'Coupon Deactivated')
                                ->info()
                                ->send();
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
