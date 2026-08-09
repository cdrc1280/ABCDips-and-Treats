<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static string|\UnitEnum|null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->components([
                TextInput::make('name')->disabled(),
                TextInput::make('email')->disabled(),
                TextInput::make('phone')->disabled(),
                TextInput::make('subject')->disabled()->columnSpanFull(),
                Textarea::make('message')->disabled()->rows(5)->columnSpanFull(),
                Select::make('status')
                    ->options(['unread' => 'Unread', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived']),
                Textarea::make('admin_notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('subject')->limit(40),
                TextColumn::make('status')->badge()
                    ->colors(['danger' => 'unread', 'warning' => 'read', 'success' => 'replied', 'gray' => 'archived']),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['unread' => 'Unread', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived']),
            ])
            ->actions([
                Action::make('mark_replied')
                    ->label('Replied ✉️')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(ContactMessage $record) => $record->status !== 'replied')
                    ->action(function (ContactMessage $record) {
                        $record->update(['status' => 'replied']);
                        Notification::make()
                            ->title('Contact Message Marked as Replied ✉️')
                            ->success()
                            ->send();
                    }),
                Action::make('mark_read')
                    ->label('Read 👁️')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn(ContactMessage $record) => $record->status === 'unread')
                    ->action(function (ContactMessage $record) {
                        $record->update(['status' => 'read']);
                        Notification::make()
                            ->title('Contact Message Marked as Read')
                            ->info()
                            ->send();
                    }),
                EditAction::make()->label('View/Edit'),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'edit' => EditContactMessage::route('/{record}/edit'),
        ];
    }
}
