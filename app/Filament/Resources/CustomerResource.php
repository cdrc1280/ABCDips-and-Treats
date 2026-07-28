<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|\UnitEnum|null $navigationGroup = 'Store';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Customer';
    protected static ?string $pluralLabel = 'Customers';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::query()->whereHas('roles', fn($q) => $q->where('name', 'customer')))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('created_at')->label('Joined')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
        ];
    }
}
