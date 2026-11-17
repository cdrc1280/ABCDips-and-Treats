<?php

namespace App\Domains\Employee\Resources;

use App\Domains\Employee\Models\Attendance;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class AttendanceResource extends Resource
{
    public function __construct(
        public string $id,
        public string $employeeId,
        public ?string $employeeName,
        public ?string $date,
        public string $status,
        public ?string $statusLabel,
        public ?string $timeIn,
        public ?string $timeOut,
        public float $hoursWorked,
        public bool $isOvertime,
        public float $overtimeHours,
        public bool $isHoliday,
        public ?string $notes,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Attendance $attendance, array $context = []): self
    {
        return new self(
            id: $attendance->id,
            employeeId: $attendance->employee_id,
            employeeName: $attendance->employee?->full_name,
            date: self::date($attendance->date),
            status: (string) $attendance->status,
            statusLabel: self::option(['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'half_day' => 'Half day', 'on_leave' => 'On leave'], $attendance->status),
            timeIn: $attendance->time_in ? substr((string) $attendance->time_in, 0, 5) : null,
            timeOut: $attendance->time_out ? substr((string) $attendance->time_out, 0, 5) : null,
            hoursWorked: (float) $attendance->hours_worked,
            isOvertime: (bool) $attendance->is_overtime,
            overtimeHours: (float) $attendance->overtime_hours,
            isHoliday: (bool) $attendance->is_holiday,
            notes: $attendance->notes,
            createdAt: $attendance->created_at?->toIso8601String(),
            updatedAt: $attendance->updated_at?->toIso8601String(),
        );
    }

    private static function date(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : substr((string) $value, 0, 10);
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
