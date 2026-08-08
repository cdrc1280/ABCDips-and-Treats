<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatEscalationResource\Pages\EditChatEscalation;
use App\Filament\Resources\ChatEscalationResource\Pages\ListChatEscalations;
use App\Models\ChatEscalation;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ChatEscalationResource extends Resource
{
    protected static ?string $model = ChatEscalation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Customer Engagement';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'open')->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('conversation')
                    ->content(function ($record) {
                        if (!$record || !$record->conversation)
                            return '';
                        $html = '';
                        foreach ($record->conversation as $message) {
                            $role = $message['role'] === 'user' ? 'User' : 'AI';
                            $html .= "<strong>{$role}:</strong> " . e($message['content']) . "<br><br>";
                        }
                        return new HtmlString($html);
                    })
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                    ])
                    ->required(),
                Textarea::make('admin_notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('guest_name')
                    ->getStateUsing(fn(ChatEscalation $record) => $record->guest_name ?? $record->user?->name)
                    ->searchable(),
                TextColumn::make('guest_email')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'resolved' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatEscalations::route('/'),
            'edit' => EditChatEscalation::route('/{record}/edit'),
        ];
    }
}
