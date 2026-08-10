<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages\CreatePayroll;
use App\Filament\Resources\PayrollResource\Pages\EditPayroll;
use App\Filament\Resources\PayrollResource\Pages\ListPayrolls;
use App\Models\Payroll;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Batch Summary')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('payroll_number')
                            ->label('Payroll Number (Auto)')
                            ->default(fn() => 'PAY-' . date('Ym') . '-' . str_pad((string) (Payroll::where('payroll_number', 'like', 'PAY-' . date('Ym') . '-%')->count() + 1), 3, '0', STR_PAD_LEFT))
                            ->required()
                            ->unique(Payroll::class, 'payroll_number', ignoreRecord: true),
                        DatePicker::make('period_start')->required(),
                        DatePicker::make('period_end')->required(),
                        Select::make('status')
                            ->options([
                                Payroll::STATUS_DRAFT => 'Draft',
                                Payroll::STATUS_APPROVED => 'Approved',
                                Payroll::STATUS_PAID => 'Paid',
                            ])
                            ->required(),
                        TextInput::make('total_gross')->label('Total Gross (₱)')->numeric()->minValue(0)->prefix('₱')->extraInputAttributes(['inputmode' => 'decimal']),
                        TextInput::make('total_deductions')->label('Total Deductions (₱)')->numeric()->minValue(0)->prefix('₱')->extraInputAttributes(['inputmode' => 'decimal']),
                        TextInput::make('total_net')->label('Total Net Pay (₱)')->numeric()->minValue(0)->prefix('₱')->extraInputAttributes(['inputmode' => 'decimal']),
                    ])->columns(2),

                Section::make('Employee Payslips (Philippine SSS, PhilHealth, Pag-IBIG, Tax Deductions)')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('items')
                            ->relationship()
                            ->components([
                                Select::make('employee_id')
                                    ->relationship('employee', 'first_name')
                                    ->required(),
                                TextInput::make('basic_pay')->numeric()->minValue(0)->prefix('₱')->required()->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('sss_deduction')->label('SSS (₱)')->numeric()->minValue(0)->prefix('₱')->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('philhealth_deduction')->label('PhilHealth (₱)')->numeric()->minValue(0)->prefix('₱')->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('pagibig_deduction')->label('Pag-IBIG (₱)')->numeric()->minValue(0)->prefix('₱')->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('withholding_tax')->label('Tax (₱)')->numeric()->minValue(0)->prefix('₱')->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('gross_pay')->numeric()->minValue(0)->prefix('₱')->required()->extraInputAttributes(['inputmode' => 'decimal']),
                                TextInput::make('net_pay')->label('Net Take Home (₱)')->numeric()->minValue(0)->prefix('₱')->required()->extraInputAttributes(['inputmode' => 'decimal']),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payroll_number')->searchable()->sortable()->weight('bold'),
                TextColumn::make('period_start')->date(),
                TextColumn::make('period_end')->date(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        Payroll::STATUS_DRAFT => 'warning',
                        Payroll::STATUS_APPROVED => 'info',
                        Payroll::STATUS_PAID => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('total_gross')->money('PHP')->sortable(),
                TextColumn::make('total_deductions')->money('PHP')->sortable(),
                TextColumn::make('total_net')->money('PHP')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                Action::make('mark_paid')
                    ->label('Approve & Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Payroll $record) => $record->status !== Payroll::STATUS_PAID)
                    ->action(function (Payroll $record) {
                        $payrollService = app(PayrollService::class);
                        $payrollService->approveAndPay($record);

                        Notification::make()
                            ->title("Payroll {$record->payroll_number} paid!")
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
            'index' => ListPayrolls::route('/'),
            'create' => CreatePayroll::route('/create'),
            'edit' => EditPayroll::route('/{record}/edit'),
        ];
    }
}
