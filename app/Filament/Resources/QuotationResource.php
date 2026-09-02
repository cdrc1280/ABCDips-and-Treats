<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages\CreateQuotation;
use App\Filament\Resources\QuotationResource\Pages\EditQuotation;
use App\Filament\Resources\QuotationResource\Pages\ListQuotations;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Quotation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Production & Purchasing';

    protected static ?string $navigationLabel = 'Quotations';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Quotation Details')
                    ->description('Standard quotation for products and supplies. If accepted, it can be converted directly into a Purchase Order (PO).')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('quotation_number')
                            ->label('Quotation #')
                            ->default(fn() => 'QT-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                            ->readOnly()
                            ->required(),

                        TextInput::make('client_name')
                            ->label('Client / Recipient Name')
                            ->placeholder('e.g. Rustan\'s / Corporate Account')
                            ->maxLength(255),

                        Select::make('supplier_id')
                            ->label('Supplier / Vendor (Optional)')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('status')
                            ->options([
                                Quotation::STATUS_DRAFT     => 'Draft',
                                Quotation::STATUS_SENT      => 'Sent to Client',
                                Quotation::STATUS_ACCEPTED  => 'Accepted',
                                Quotation::STATUS_REJECTED  => 'Rejected',
                                Quotation::STATUS_CONVERTED => 'Converted to PO',
                            ])
                            ->default(Quotation::STATUS_DRAFT)
                            ->required(),

                        DatePicker::make('quotation_date')
                            ->label('Quotation Date')
                            ->default(now()),

                        DatePicker::make('valid_until')
                            ->label('Valid Until (Expiry Date)')
                            ->default(now()->addDays(30)),

                        TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        TextInput::make('tax')
                            ->numeric()
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        TextInput::make('total')
                            ->numeric()
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(2),

                Section::make('Quotation Line Items')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('items')
                            ->relationship('items')
                            ->components([
                                Select::make('ingredient_id')
                                    ->label('Item / Ingredient')
                                    ->relationship('ingredient', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $ing = Ingredient::find($state);
                                            if ($ing) {
                                                $set('unit_price', $ing->item_price);
                                            }
                                        }
                                    }),

                                TextInput::make('item_description')
                                    ->label('Description / Custom Item')
                                    ->placeholder('e.g. Led AR111 bulb 12w,3000K'),

                                TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.001)
                                    ->required(),

                                TextInput::make('unit')
                                    ->label('Unit')
                                    ->default('pcs'),

                                TextInput::make('unit_price')
                                    ->label('Unit Price (₱)')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotation_number')->searchable()->sortable()->weight('bold'),
                TextColumn::make('client_name')->label('Client / Recipient')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        Quotation::STATUS_DRAFT     => 'warning',
                        Quotation::STATUS_SENT      => 'info',
                        Quotation::STATUS_ACCEPTED  => 'success',
                        Quotation::STATUS_REJECTED  => 'danger',
                        Quotation::STATUS_CONVERTED => 'primary',
                        default                     => 'gray',
                    }),
                TextColumn::make('total')->money('PHP')->sortable(),
                TextColumn::make('quotation_date')->date(),
                TextColumn::make('valid_until')->date(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                Action::make('convert_to_po')
                    ->label('Convert to PO')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->visible(fn(Quotation $record) => $record->status !== Quotation::STATUS_CONVERTED)
                    ->requiresConfirmation()
                    ->modalHeading('Convert Quotation to Purchase Order')
                    ->modalDescription('This will create a new linked Purchase Order directly from this quotation.')
                    ->action(function (Quotation $record) {
                        $po = PurchaseOrder::create([
                            'po_number'           => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                            'supplier_id'         => $record->supplier_id ?? \App\Models\Supplier::first()?->id ?? 1,
                            'quotation_id'        => $record->id,
                            'status'              => PurchaseOrder::STATUS_DRAFT,
                            'subtotal'            => $record->subtotal,
                            'tax'                 => $record->tax,
                            'total'               => $record->total,
                            'notes'               => "Generated from Quotation #{$record->quotation_number}",
                        ]);

                        foreach ($record->items as $item) {
                            PurchaseOrderItem::create([
                                'purchase_order_id' => $po->id,
                                'ingredient_id'     => $item->ingredient_id ?? Ingredient::first()?->id ?? 1,
                                'qty_ordered'       => $item->qty,
                                'qty_received'      => 0,
                                'unit_cost'         => $item->unit_price,
                                'subtotal'          => $item->qty * $item->unit_price,
                            ]);
                        }

                        $record->update(['status' => Quotation::STATUS_CONVERTED]);

                        Notification::make()
                            ->title("Quotation {$record->quotation_number} converted to PO {$po->po_number}!")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'edit'   => EditQuotation::route('/{record}/edit'),
        ];
    }
}
