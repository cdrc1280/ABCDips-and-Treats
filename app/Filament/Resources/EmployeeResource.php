<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use App\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use App\Models\Employee;
use Filament\Actions\EditAction;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Payroll & HR';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employee Profile')
                    ->components([
                        TextInput::make('employee_number')->required()->unique(Employee::class, 'employee_number', ignoreRecord: true),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable(),
                        TextInput::make('first_name')->required(),
                        TextInput::make('last_name')->required(),
                        TextInput::make('email')->email()->required(),
                        TextInput::make('phone'),
                        Select::make('role_title')
                            ->options(['Head Baker' => 'Head Baker', 'Assistant Baker' => 'Assistant Baker', 'Cashier' => 'Cashier', 'Pastry Staff' => 'Pastry Staff'])
                            ->required(),
                        Select::make('employment_type')
                            ->options(['full_time' => 'Full-Time Regular', 'part_time' => 'Part-Time Flex'])
                            ->required(),
                        TextInput::make('basic_monthly_salary')->label('Basic Monthly Salary (₱)')->numeric()->prefix('₱')->required(),
                        DatePicker::make('hired_at'),
                        Toggle::make('is_active')->default(true),
                    ])->columnSpanFull(),
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
                TextColumn::make('hired_at')->date(),
            ])
            ->actions([
                EditAction::make(),
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
