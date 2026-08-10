<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages\CreateAttendance;
use App\Filament\Resources\AttendanceResource\Pages\EditAttendance;
use App\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use App\Models\Attendance;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|\UnitEnum|null $navigationGroup = 'HR & Payroll';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance Record')->components([
                Select::make('employee_id')
                    ->relationship('employee', 'first_name')
                    ->required()
                    ->searchable(),
                DatePicker::make('date')->required(),
                TimePicker::make('time_in')->seconds(false),
                TimePicker::make('time_out')->seconds(false),
                TextInput::make('hours_worked')->numeric()->default(8),
                Select::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                        'on_leave' => 'On Leave',
                    ])
                    ->required()
                    ->default('present'),
                Toggle::make('is_holiday')->label('Holiday'),
                Toggle::make('is_overtime')->label('Overtime'),
                TextInput::make('overtime_hours')->numeric()->default(0),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('time_in')->time(),
                TextColumn::make('time_out')->time(),
                TextColumn::make('hours_worked')->suffix('h'),
                TextColumn::make('status')->badge()
                    ->colors([
                        'success' => 'present',
                        'warning' => ['late', 'half_day'],
                        'danger' => 'absent',
                        'info' => 'on_leave',
                    ]),
                IconColumn::make('is_holiday')->boolean()->label('Holiday'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'absent' => 'Absent',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                        'on_leave' => 'On Leave',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
