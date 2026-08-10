<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages\CreateReview;
use App\Filament\Resources\ReviewResource\Pages\EditReview;
use App\Filament\Resources\ReviewResource\Pages\ListReviews;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static string|\UnitEnum|null $navigationGroup = 'Orders & Sales';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details')
                    ->columnSpanFull()
                    ->components([
                        Select::make('product_id')
                            ->relationship('product', 'name'),
                        TextInput::make('reviewer_name')->required(),
                        TextInput::make('reviewer_email')->email()->required(),
                        Select::make('rating')
                            ->options([1 => '1 Star', 2 => '2 Stars', 3 => '3 Stars', 4 => '4 Stars', 5 => '5 Stars'])
                            ->required(),
                        TextInput::make('title')->columnSpanFull(),
                        Textarea::make('comment')->required()->columnSpanFull(),
                        Toggle::make('is_approved')->default(true),
                        Toggle::make('is_featured')->default(false),
                        Toggle::make('is_verified_buyer')->default(false),
                        Toggle::make('is_anonymous')->label('Post Anonymously')->default(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('reviewer_name')
                    ->searchable()
                    ->formatStateUsing(fn($state, Review $record) => $record->is_anonymous ? "{$state} (Anonymous)" : $state),

                TextColumn::make('rating')
                    ->sortable()
                    ->formatStateUsing(fn(int $state) => str_repeat('⭐', $state)),

                TextColumn::make('title')
                    ->limit(30),

                IconColumn::make('is_anonymous')
                    ->boolean()
                    ->label('Anonymous'),

                IconColumn::make('is_approved')
                    ->boolean()
                    ->label('Approved'),

                IconColumn::make('is_verified_buyer')
                    ->boolean()
                    ->label('Verified'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_approved'),
                TernaryFilter::make('is_featured'),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('toggle_approval')
                    ->label(fn(Review $record) => $record->is_approved ? 'Reject' : 'Approve')
                    ->color(fn(Review $record) => $record->is_approved ? 'danger' : 'success')
                    ->icon(fn(Review $record) => $record->is_approved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->action(function (Review $record) {
                        $record->update(['is_approved' => !$record->is_approved]);
                        Notification::make()
                            ->title($record->is_approved ? 'Review Approved' : 'Review Rejected')
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
