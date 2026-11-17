<?php

namespace App\Domains\Employee\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class AttendanceData extends Data
{
    public function __construct(
        public string $employeeId,
        public string $date,
        public ?string $status = 'present',
        public ?string $timeIn = null,
        public ?string $timeOut = null,
        public ?float $hoursWorked = 8,
        public bool $isOvertime = false,
        public ?float $overtimeHours = 0,
        public bool $isHoliday = false,
        public ?string $notes = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['employee_id'] = ($properties['employee_id'] ?? '') === '' ? null : (string) $properties['employee_id'];
        $properties['date'] = trim((string) ($properties['date'] ?? '')) ?: null;
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'present';
        $properties['time_in'] = trim((string) ($properties['time_in'] ?? '')) ?: null;
        $properties['time_out'] = trim((string) ($properties['time_out'] ?? '')) ?: null;
        $properties['hours_worked'] = ($properties['hours_worked'] ?? '') === '' ? 8 : (float) $properties['hours_worked'];
        $properties['is_overtime'] = filter_var($properties['is_overtime'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['overtime_hours'] = ($properties['overtime_hours'] ?? '') === '' ? 0 : (float) $properties['overtime_hours'];
        $properties['is_holiday'] = filter_var($properties['is_holiday'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('attendance');

        return [
            'employee_id' => ['bail', 'required', 'uuid', Rule::exists('employees', 'id')->whereNull('deleted_at')],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(['present', 'absent', 'late', 'half_day', 'on_leave'])],
            'time_in' => ['nullable', 'date_format:H:i'],
            'time_out' => ['nullable', 'date_format:H:i', 'after:time_in'],
            'hours_worked' => ['required', 'numeric', 'min:0', 'max:24'],
            'is_overtime' => ['boolean'],
            'overtime_hours' => ['required', 'numeric', 'min:0', 'max:24'],
            'is_holiday' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [
            'time_out.after' => __('Time out must be after time in.'),
        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'employee_id' => __('employee'),
            'date' => __('date'),
            'status' => __('status'),
            'time_in' => __('time in'),
            'time_out' => __('time out'),
            'hours_worked' => __('hours worked'),
            'is_overtime' => __('overtime'),
            'overtime_hours' => __('overtime hours'),
            'is_holiday' => __('holiday'),
            'notes' => __('notes'),
        ];
    }
}
