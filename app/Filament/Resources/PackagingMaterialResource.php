<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackagingMaterialResource\Pages\CreatePackagingMaterial;
use App\Filament\Resources\PackagingMaterialResource\Pages\EditPackagingMaterial;
use App\Filament\Resources\PackagingMaterialResource\Pages\ListPackagingMaterials;
use App\Models\PackagingMaterial;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Illuminate\Support\Str;

class PackagingMaterialResource extends Resource
{
    protected static ?string $model = PackagingMaterial::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';
    protected static string|\UnitEnum|null $navigationGroup = 'Inventory & Supplies';
    protected static ?string $navigationLabel = 'Packaging Materials';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Material Details')->components([
                TextInput::make('name')->required(),
                TextInput::make('sku')
                    ->label('SKU (Auto-generated)')
                    ->default(fn() => 'PKG-' . strtoupper(Str::random(6)))
                    ->readOnly()
                    ->required()
                    ->unique(PackagingMaterial::class, 'sku', ignoreRecord: true),
                Select::make('type')
                    ->options(['box' => 'Box', 'bag' => 'Bag', 'board' => 'Board', 'container' => 'Container', 'ribbon' => 'Ribbon', 'label' => 'Label', 'sticker' => 'Sticker', 'tape' => 'Tape', 'wrap' => 'Wrap', 'other' => 'Other'])
                    ->required(),
                TextInput::make('unit')->default('pcs')->required(),
                TextInput::make('cost_per_unit')->numeric()->prefix('₱'),
                TextInput::make('stock_qty')->numeric()->label('Stock Qty'),
                TextInput::make('min_stock_qty')->numeric()->label('Min Stock'),
                Textarea::make('notes')->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ])->columnSpanFull()->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                TextColumn::make('sku')->searchable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('unit'),
                TextColumn::make('cost_per_unit')->money('PHP')->sortable(),
                TextColumn::make('stock_qty')->label('Stock')->sortable()
                    ->color(fn($record) => $record->stock_qty <= $record->min_stock_qty ? 'danger' : 'success'),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options(['box' => 'Box', 'bag' => 'Bag', 'board' => 'Board', 'container' => 'Container', 'ribbon' => 'Ribbon', 'label' => 'Label', 'sticker' => 'Sticker', 'tape' => 'Tape', 'wrap' => 'Wrap', 'other' => 'Other']),
            ])
            ->actions([
                Action::make('quick_restock')
                    ->label('Restock 📦')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('add_qty')
                            ->label('Add Stock Quantity')
                            ->integer()
                            ->minValue(1)
                            ->required()
                            ->suffix(fn(PackagingMaterial $record) => $record->unit),
                    ])
                    ->action(function (PackagingMaterial $record, array $data) {
                        $record->increment('stock_qty', $data['add_qty']);
                        Notification::make()
                            ->title("Added {$data['add_qty']} {$record->unit} to {$record->name} stock! 📦")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPackagingMaterials::route('/'),
            'create' => CreatePackagingMaterial::route('/create'),
            'edit' => EditPackagingMaterial::route('/{record}/edit'),
        ];
    }
}
