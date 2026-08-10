<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages\CreateSupplier;
use App\Filament\Resources\SupplierResource\Pages\EditSupplier;
use App\Filament\Resources\SupplierResource\Pages\ListSuppliers;
use App\Models\Supplier;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory & Supplies';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Supplier Details & Contact')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label('Supplier / Company Name')
                            ->placeholder('e.g. San Miguel Foods, Bakers Depot')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->placeholder('e.g. Juan dela Cruz')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Mobile Phone Number (11 Digits)')
                            ->placeholder('09171234567')
                            ->tel()
                            ->numeric()
                            ->length(11)
                            ->regex('/^09\d{9}$/')
                            ->validationMessages([
                                'length' => 'Supplier phone number must be exactly 11 digits.',
                                'numeric' => 'Supplier phone number must contain digits only.',
                                'regex' => 'Supplier phone number must be a valid 11-digit Philippine mobile number starting with 09 (e.g. 09171234567).',
                            ])
                            ->required()
                            ->extraInputAttributes(['inputmode' => 'numeric', 'maxlength' => '11']),
                        TextInput::make('payment_terms')
                            ->label('Payment Terms')
                            ->default('Net 30')
                            ->required(),
                        Textarea::make('address')
                            ->label('Company Address')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Internal Notes / Supply Info')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('contact_person')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('payment_terms'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
