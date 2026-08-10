<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use App\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'HR & Payroll';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employee Profile')
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('employee_number')
                            ->label('Employee Number (Auto)')
                            ->default(fn() => 'EMP-' . str_pad((string) ((Employee::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT))
                            ->required()
                            ->unique(Employee::class, 'employee_number', ignoreRecord: true),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable(),
                        TextInput::make('first_name')->required()->maxLength(255),
                        TextInput::make('last_name')->required()->maxLength(255),
                        TextInput::make('email')->email()->required()->maxLength(255),
                        TextInput::make('phone')
                            ->label('Mobile Phone (11 Digits)')
                            ->placeholder('09171234567')
                            ->tel()
                            ->numeric()
                            ->length(11)
                            ->regex('/^09\d{9}$/')
                            ->required()
                            ->extraInputAttributes(['inputmode' => 'numeric', 'maxlength' => '11']),
                        Select::make('role_title')
                            ->options(['Head Baker' => 'Head Baker', 'Assistant Baker' => 'Assistant Baker', 'Cashier' => 'Cashier', 'Pastry Staff' => 'Pastry Staff'])
                            ->required(),
                        Select::make('employment_type')
                            ->options(['full_time' => 'Full-Time Regular', 'part_time' => 'Part-Time Flex'])
                            ->required(),
                        TextInput::make('basic_monthly_salary')->label('Basic Monthly Salary (₱)')->numeric()->minValue(0)->prefix('₱')->required()->extraInputAttributes(['inputmode' => 'decimal']),
                        DatePicker::make('hired_at')->required(),
                        Toggle::make('is_active')->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_number')->searchable()->sortable()->weight('bold'),
                TextColumn::make('full_name')->label('Employee Name')->searchable(['first_name', 'last_name']),
                TextColumn::make('role_title')->badge(),
                TextColumn::make('employment_type')->formatStateUsing(fn($state) => str_replace('_', ' ', strtoupper($state))),
                TextColumn::make('basic_monthly_salary')->money('PHP')->sortable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make(),
                Action::make('toggle_active')
                    ->label(fn(Employee $record) => $record->is_active ? 'Deactivate' : 'Activate Employee')
                    ->icon('heroicon-o-power')
                    ->color(fn(Employee $record) => $record->is_active ? 'danger' : 'success')
                    ->action(function (Employee $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Employee Profile Activated 👤' : 'Employee Profile Deactivated')
                            ->info()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
