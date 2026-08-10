<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\AuditLog;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null $navigationGroup = 'System Administration';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Audit Trail Details')->components([
                TextInput::make('user.name')->label('User')->readOnly(),
                TextInput::make('event')->readOnly(),
                TextInput::make('auditable_type')->label('Model / Resource')->readOnly(),
                TextInput::make('ip_address')->label('IP Address')->readOnly(),
                Textarea::make('description')->readOnly()->columnSpanFull(),
                TextInput::make('user_agent')->label('Browser / Device User Agent')->readOnly()->columnSpanFull(),
                KeyValue::make('old_values')->label('Original Values Before Change')->readOnly(),
                KeyValue::make('new_values')->label('New Modified Values')->readOnly(),
            ])->columnSpanFull()->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable()->default('System'),
                TextColumn::make('event')->badge()->searchable(),
                TextColumn::make('auditable_type')->label('Resource')->formatStateUsing(fn($state) => class_basename($state ?? ''))->searchable(),
                TextColumn::make('description')->limit(60),
                TextColumn::make('ip_address'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->options(['login' => 'Login', 'logout' => 'Logout', 'created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted']),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
