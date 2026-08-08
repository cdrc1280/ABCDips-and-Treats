<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuggestionResource\Pages\EditSuggestion;
use App\Filament\Resources\SuggestionResource\Pages\ListSuggestions;
use App\Models\Suggestion;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    protected static string|\UnitEnum|null $navigationGroup = 'Customer Engagement';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->options([
                        'product_idea' => 'Product Idea',
                        'service_feedback' => 'Service Feedback',
                        'feature_request' => 'Feature Request',
                        'other' => 'Other',
                    ])
                    ->disabled(),
                TextInput::make('name')->readOnlyOn('edit'),
                TextInput::make('email')->readOnlyOn('edit'),
                TextInput::make('subject')->readOnlyOn('edit'),
                Textarea::make('message')->readOnlyOn('edit')->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'new' => 'New',
                        'reviewing' => 'Reviewing',
                        'implemented' => 'Implemented',
                        'declined' => 'Declined',
                    ]),
                Textarea::make('admin_notes')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('category')
                    ->badge(),
                TextColumn::make('subject')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'info',
                        'reviewing' => 'warning',
                        'implemented' => 'success',
                        'declined' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'product_idea' => 'Product Idea',
                        'service_feedback' => 'Service Feedback',
                        'feature_request' => 'Feature Request',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'reviewing' => 'Reviewing',
                        'implemented' => 'Implemented',
                        'declined' => 'Declined',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuggestions::route('/'),
            'edit' => EditSuggestion::route('/{record}/edit'),
        ];
    }
}
