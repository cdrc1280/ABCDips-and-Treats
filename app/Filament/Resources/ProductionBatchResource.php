<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionBatchResource\Pages\CreateProductionBatch;
use App\Filament\Resources\ProductionBatchResource\Pages\EditProductionBatch;
use App\Filament\Resources\ProductionBatchResource\Pages\ListProductionBatches;
use App\Models\ProductionBatch;
use App\Models\Recipe;
use App\Services\ProductionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductionBatchResource extends Resource
{
    protected static ?string $model = ProductionBatch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    protected static string|\UnitEnum|null $navigationGroup = 'Production & Purchasing';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Production Run Details')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('batch_number')->readOnly(),
                        Select::make('recipe_id')
                            ->relationship('recipe', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(
                                fn($state, Set $set) =>
                                $state ? $set('product_id', Recipe::find($state)?->product_id) : null
                            ),
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required(),
                        TextInput::make('planned_qty')->label('Planned Batches')->numeric()->required()->default(1),
                        TextInput::make('actual_yield_qty')->label('Actual Finished Units')->numeric(),
                        Select::make('status')
                            ->options([
                                ProductionBatch::STATUS_PLANNED => 'Planned',
                                ProductionBatch::STATUS_IN_PREP => 'In Prep',
                                ProductionBatch::STATUS_BAKING => 'Baking in Oven',
                                ProductionBatch::STATUS_COMPLETED => 'Completed (Stock Added)',
                                ProductionBatch::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->required(),
                        Select::make('baker_user_id')
                            ->relationship('baker', 'name')
                            ->label('Head Baker'),
                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_number')->searchable()->sortable()->weight('bold'),
                TextColumn::make('recipe.name')->searchable(),
                TextColumn::make('planned_qty')->label('Planned'),
                TextColumn::make('actual_yield_qty')->label('Yield'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        ProductionBatch::STATUS_PLANNED => 'warning',
                        ProductionBatch::STATUS_IN_PREP => 'info',
                        ProductionBatch::STATUS_BAKING => 'primary',
                        ProductionBatch::STATUS_COMPLETED => 'success',
                        ProductionBatch::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('baker.name')->label('Baker'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('complete_batch')
                    ->label('Complete & Credit Inventory')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(ProductionBatch $record) => $record->status !== ProductionBatch::STATUS_COMPLETED)
                    ->action(function (ProductionBatch $record) {
                        $prodService = app(ProductionService::class);
                        $prodService->completeBatch($record);

                        Notification::make()
                            ->title("Batch {$record->batch_number} completed!")
                            ->body("Deducted raw ingredients & credited finished product inventory.")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductionBatches::route('/'),
            'create' => CreateProductionBatch::route('/create'),
            'edit' => EditProductionBatch::route('/{record}/edit'),
        ];
    }
}
