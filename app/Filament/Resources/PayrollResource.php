<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages\CreatePayroll;
use App\Filament\Resources\PayrollResource\Pages\EditPayroll;
use App\Filament\Resources\PayrollResource\Pages\ListPayrolls;
use App\Models\Payroll;
use App\Services\PayrollService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll & HR';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Batch Summary')
                    ->components([
                        TextInput::make('payroll_number')->readOnly(),
                        DatePicker::make('period_start')->required(),
                        DatePicker::make('period_end')->required(),
                        Select::make('status')
                            ->options([
                                Payroll::STATUS_DRAFT => 'Draft',
                                Payroll::STATUS_APPROVED => 'Approved',
                                Payroll::STATUS_PAID => 'Paid',
                            ])
                            ->required(),
                        TextInput::make('total_gross')->label('Total Gross (₱)')->numeric()->prefix('₱'),
                        TextInput::make('total_deductions')->label('Total Deductions (₱)')->numeric()->prefix('₱'),
                        TextInput::make('total_net')->label('Total Net Pay (₱)')->numeric()->prefix('₱'),
                    ]),

                Section::make('Employee Payslips (Philippine SSS, PhilHealth, Pag-IBIG, Tax Deductions)')
                    ->components([
                        Repeater::make('items')
                            ->relationship()
                            ->components([
                                Select::make('employee_id')
                                    ->relationship('employee', 'first_name')
                                    ->required(),
                                TextInput::make('basic_pay')->numeric()->prefix('₱')->required(),
                                TextInput::make('sss_deduction')->label('SSS (₱)')->numeric()->prefix('₱'),
                                TextInput::make('philhealth_deduction')->label('PhilHealth (₱)')->numeric()->prefix('₱'),
                                TextInput::make('pagibig_deduction')->label('Pag-IBIG (₱)')->numeric()->prefix('₱'),
                                TextInput::make('withholding_tax')->label('Tax (₱)')->numeric()->prefix('₱'),
                                TextInput::make('gross_pay')->numeric()->prefix('₱')->required(),
                                TextInput::make('net_pay')->label('Net Take Home (₱)')->numeric()->prefix('₱')->required(),
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
            ->actions([
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
