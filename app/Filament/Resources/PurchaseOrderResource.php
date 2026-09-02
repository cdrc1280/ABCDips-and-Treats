<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages\CreatePurchaseOrder;
use App\Filament\Resources\PurchaseOrderResource\Pages\EditPurchaseOrder;
use App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Services\PoPdfParserService;
use App\Services\PurchasingService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'Production & Purchasing';

    protected static ?string $navigationLabel = 'Purchase Orders';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // 1. PDF UPLOAD & AUTO-VERIFIER (ACCEPTS NORMAL PO & CONFORME PO)
                Section::make('📄 Purchase Order PDF Upload & Auto-Verifier')
                    ->description('Upload any Purchase Order PDF (Normal PO or Conforme PO). Our verifier automatically checks for Conforme acceptance terms, authorized signature, line items, and totals.')
                    ->columnSpanFull()
                    ->components([
                        FileUpload::make('pdf_path')
                            ->label('Upload Purchase Order PDF')
                            ->disk('public')
                            ->directory('purchase_orders')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(20480)
                            ->helperText('Upload PO PDF file. Conforme acceptance clauses, authorized signature, supplier details, items, and pricing will be extracted automatically.')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!empty($state)) {
                                    $parser = app(PoPdfParserService::class);
                                    $result = $parser->parsePoPdf($state);
                                    if ($result['success']) {
                                        $set('is_conforme', $result['is_conforme']);
                                        $set('po_type', $result['po_type']);
                                        $set('is_signature_verified', $result['is_signature_verified']);
                                        
                                        if ($result['is_conforme']) {
                                            $set('conforme_signatory', $result['conforme_signatory']);
                                            $set('conforme_business_name', $result['conforme_business_name']);
                                            if (!empty($result['conforme_date'])) {
                                                $set('conforme_date', $result['conforme_date']);
                                            }
                                            $set('quotation_id', null); // Conforme PO bypasses quotation!
                                        }

                                        if (!empty($result['po_number'])) {
                                            $set('po_number', $result['po_number']);
                                        }
                                        if (!empty($result['supplier_id'])) {
                                            $set('supplier_id', $result['supplier_id']);
                                        }
                                        if (!empty($result['expected_delivery_date'])) {
                                            $set('expected_delivery_date', $result['expected_delivery_date']);
                                        }
                                        if (!empty($result['payment_terms'])) {
                                            $set('payment_terms', $result['payment_terms']);
                                        }
                                        if ($result['subtotal'] > 0) {
                                            $set('subtotal', $result['subtotal']);
                                        }
                                        if ($result['tax'] > 0) {
                                            $set('tax', $result['tax']);
                                        }
                                        if ($result['total'] > 0) {
                                            $set('total', $result['total']);
                                        }
                                        if (!empty($result['items'])) {
                                            $set('items', $result['items']);
                                        }

                                        Notification::make()
                                            ->title($result['is_conforme'] ? '✅ Conforme PO Verified (Quotation Bypassed)' : '📄 Standard PO Parsed Successfully')
                                            ->body($result['is_conforme']
                                                ? "Signatory: {$result['conforme_signatory']} | Conforme honored without quotation requirement."
                                                : "Extracted PO #{$result['po_number']}. You can link to a quotation below.")
                                            ->success()
                                            ->send();
                                    }
                                }
                            }),
                    ]),

                // 2. CONFORME PO VERIFIER SECTION (SPECIAL CASE)
                Section::make('✍️ Conforme Verification & Quotation Bypass')
                    ->description('Special case: Conforme POs with signed acceptance terms are legally honored on their own without requiring a prior quotation.')
                    ->columnSpanFull()
                    ->components([
                        Toggle::make('is_conforme')
                            ->label('Conforme Purchase Order (Bypasses Quotation)')
                            ->helperText('Enable if this PO contains Conforme / Terms Acceptance with authorized signature. When enabled, quotation linking is automatically bypassed and hidden.')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('quotation_id', null);
                                    $set('po_type', 'conforme');
                                } else {
                                    $set('po_type', 'normal');
                                }
                            }),

                        Grid::make(3)
                            ->visible(fn(callable $get) => (bool) $get('is_conforme'))
                            ->schema([
                                TextInput::make('conforme_signatory')
                                    ->label('Signatory Over Printed Name')
                                    ->placeholder('e.g. Emmanuel Joshua B. Serrano / CHARMAYNE B. SERRANO')
                                    ->required(fn(callable $get) => (bool) $get('is_conforme')),

                                TextInput::make('conforme_business_name')
                                    ->label('Conforme Business Name')
                                    ->placeholder('e.g. HUENICS INDUSTRIAL SALES INC.')
                                    ->default('HUENICS INDUSTRIAL SALES INC.'),

                                DatePicker::make('conforme_date')
                                    ->label('Conforme Signed Date')
                                    ->default(now()),

                                Toggle::make('is_signature_verified')
                                    ->label('Authorized Signature Verified')
                                    ->default(true)
                                    ->helperText('Confirmed valid authorized signature in document.'),
                            ]),

                        Placeholder::make('conforme_info_banner')
                            ->label('')
                            ->visible(fn(callable $get) => (bool) $get('is_conforme'))
                            ->content(new HtmlString('
                                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-700 rounded-xl text-xs text-emerald-900 dark:text-emerald-200 flex items-start gap-2">
                                    <span class="text-base">🛡️</span>
                                    <div>
                                        <strong class="font-bold">Legally Binding Conforme PO:</strong> This purchase order contains verified acceptance terms and authorized signature. The <em>Link to Quotation</em> requirement is bypassed.
                                    </div>
                                </div>
                            ')),
                    ]),

                // 3. PURCHASE ORDER GENERAL DETAILS
                Section::make('Purchase Order Information')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('po_number')
                            ->label('PO Number')
                            ->default(fn() => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                            ->required(),

                        Select::make('supplier_id')
                            ->label('Supplier / Vendor')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // LINK TO QUOTATION (HIDDEN WHEN CONFORME PO IS ACTIVE!)
                        Select::make('quotation_id')
                            ->label('Link to Quotation')
                            ->relationship('quotation', 'quotation_number')
                            ->searchable()
                            ->preload()
                            ->hidden(fn(callable $get) => (bool) $get('is_conforme'))
                            ->helperText('Select originating quotation for standard POs. (Hidden when Conforme PO is verified).'),

                        TextInput::make('payment_terms')
                            ->label('Payment Terms')
                            ->placeholder('e.g. 30 Days, 30 days PDC Inclusive of VAT')
                            ->default('30 Days'),

                        DatePicker::make('expected_delivery_date')
                            ->label('Expected Delivery Date'),

                        Select::make('status')
                            ->options([
                                PurchaseOrder::STATUS_DRAFT     => 'Draft',
                                PurchaseOrder::STATUS_SENT      => 'Sent to Supplier',
                                PurchaseOrder::STATUS_RECEIVED  => 'Received & Restocked',
                                PurchaseOrder::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->default(PurchaseOrder::STATUS_DRAFT)
                            ->required(),

                        TextInput::make('subtotal')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        TextInput::make('tax')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        TextInput::make('total')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('₱')
                            ->extraInputAttributes(['inputmode' => 'decimal']),

                        Textarea::make('notes')->columnSpanFull(),
                    ])->columns(3),

                // 4. DELIVERY RECEIPT (DR) & SALES INVOICE (SI) FULFILLMENT
                Section::make('📦 Delivery Receipt (DR) & Sales Invoice (SI) Tracking')
                    ->description('Record DR # and SI # to track order fulfillment through delivery and financial settlement.')
                    ->columnSpanFull()
                    ->collapsible()
                    ->components([
                        TextInput::make('delivery_receipt_no')
                            ->label('Delivery Receipt (DR #)')
                            ->placeholder('e.g. DR-2026-0819'),

                        TextInput::make('sales_invoice_no')
                            ->label('Sales Invoice (SI #)')
                            ->placeholder('e.g. SI-2026-0819'),

                        DatePicker::make('dr_issued_at')
                            ->label('DR Issued Date'),

                        DatePicker::make('si_issued_at')
                            ->label('SI Issued Date'),

                        DatePicker::make('delivered_at')
                            ->label('Delivered / Fulfilled Date'),
                    ])->columns(3),

                // 5. ORDERED LINE ITEMS
                Section::make('Ordered Line Items')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('items')
                            ->relationship('items')
                            ->components([
                                Select::make('ingredient_id')
                                    ->label('Select Item / Ingredient')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $ing = Ingredient::find($state);
                                            if ($ing) {
                                                $set('unit_cost', $ing->item_price);
                                            }
                                        }
                                    }),
                                TextInput::make('qty_ordered')
                                    ->label('Qty Ordered')
                                    ->numeric()
                                    ->minValue(0.001)
                                    ->required()
                                    ->default(1)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('unit_cost')
                                    ->label('Unit Cost (₱)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('₱')
                                    ->required()
                                    ->default(0)
                                    ->extraInputAttributes(['inputmode' => 'decimal']),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('po_number')->searchable()->sortable()->weight('bold'),

                TextColumn::make('po_type')
                    ->label('PO Type')
                    ->badge()
                    ->formatStateUsing(fn(PurchaseOrder $record) => $record->is_conforme ? '✅ Conforme (Signed)' : 'Standard PO')
                    ->color(fn(PurchaseOrder $record) => $record->is_conforme ? 'success' : 'gray'),

                TextColumn::make('supplier.name')->searchable(),

                TextColumn::make('quotation.quotation_number')
                    ->label('Quotation #')
                    ->placeholder(fn(PurchaseOrder $record) => $record->is_conforme ? '— Bypassed (Conforme)' : '— No Quote')
                    ->color(fn(PurchaseOrder $record) => $record->is_conforme ? 'gray' : 'primary'),

                TextColumn::make('delivery_receipt_no')
                    ->label('DR #')
                    ->placeholder('—'),

                TextColumn::make('sales_invoice_no')
                    ->label('SI #')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        PurchaseOrder::STATUS_DRAFT     => 'warning',
                        PurchaseOrder::STATUS_SENT      => 'info',
                        PurchaseOrder::STATUS_RECEIVED  => 'success',
                        PurchaseOrder::STATUS_CANCELLED => 'danger',
                        default                         => 'gray',
                    }),

                TextColumn::make('total')->money('PHP')->sortable(),
                TextColumn::make('expected_delivery_date')->date(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),

                // ISSUE DR & SI ACTION
                Action::make('issue_dr_si')
                    ->label('Issue DR & SI')
                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->form([
                        TextInput::make('delivery_receipt_no')
                            ->label('Delivery Receipt Number (DR #)')
                            ->default(fn(PurchaseOrder $record) => $record->delivery_receipt_no ?: 'DR-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                            ->required(),
                        TextInput::make('sales_invoice_no')
                            ->label('Sales Invoice Number (SI #)')
                            ->default(fn(PurchaseOrder $record) => $record->sales_invoice_no ?: 'SI-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                            ->required(),
                    ])
                    ->action(function (PurchaseOrder $record, array $data) {
                        $record->update([
                            'delivery_receipt_no' => $data['delivery_receipt_no'],
                            'sales_invoice_no'    => $data['sales_invoice_no'],
                            'dr_issued_at'        => now(),
                            'si_issued_at'        => now(),
                        ]);

                        Notification::make()
                            ->title("DR & SI Issued for PO {$record->po_number}!")
                            ->success()
                            ->send();
                    }),

                // MARK AS DELIVERED / RECEIVE ACTION
                Action::make('receive_po')
                    ->label('Mark Delivered & Restock')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn(PurchaseOrder $record) => $record->status !== PurchaseOrder::STATUS_RECEIVED)
                    ->action(function (PurchaseOrder $record) {
                        $purchasingService = app(PurchasingService::class);
                        $purchasingService->receivePurchaseOrder($record);

                        $record->update([
                            'delivered_at' => now(),
                        ]);

                        Notification::make()
                            ->title("PO {$record->po_number} Marked as Delivered & Restocked!")
                            ->body("Inventory updated and stock movements recorded.")
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
            'index'  => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit'   => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
