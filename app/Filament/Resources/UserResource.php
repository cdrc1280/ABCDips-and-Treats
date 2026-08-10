<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|\UnitEnum|null $navigationGroup = 'System Administration';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'User & Role';
    protected static ?string $pluralLabel = 'System Users & Roles';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Account & Access Control')->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', ignoreRecord: true),

                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->rule('nullable|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?~`]).{8,}$/')
                    ->hint('Min 8 chars, A-Z, a-z, 0-9, special char'),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),

                FileUpload::make('avatar')
                    ->label('Profile Avatar')
                    ->avatar()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(2048)
                    ->disk('public')
                    ->directory('avatars')
                    ->visibility('public')
                    ->columnSpanFull(),

                TextInput::make('phone')
                    ->label('Mobile Phone (11 Digits)')
                    ->placeholder('09171234567')
                    ->tel()
                    ->numeric()
                    ->length(11)
                    ->regex('/^09\d{9}$/')
                    ->extraInputAttributes(['inputmode' => 'numeric', 'maxlength' => '11']),

                Textarea::make('address')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name))
                    ->getStateUsing(function (User $record) {
                        if ($record->avatar) {
                            return str_starts_with($record->avatar, 'http')
                                ? $record->avatar
                                : asset('storage/' . ltrim($record->avatar, '/'));
                        }
                        return 'https://ui-avatars.com/api/?name=' . urlencode($record->name);
                    }),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Assigned Roles')
                    ->badge()
                    ->colors([
                        'danger' => ['super_admin', 'admin'],
                        'warning' => ['head_baker', 'baker', 'manager'],
                        'info' => ['cashier', 'staff'],
                        'success' => 'customer',
                    ])
                    ->searchable(),

                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Registered Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
