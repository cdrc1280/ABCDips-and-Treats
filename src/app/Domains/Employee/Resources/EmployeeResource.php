<?php

namespace App\Domains\Employee\Resources;

use App\Domains\Employee\Models\Employee;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class EmployeeResource extends Resource
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public ?string $employeeNumber,
        public string $email,
        public ?string $phone,
        public ?string $userId,
        public ?string $userEmail,
        public string $roleTitle,
        public ?string $roleTitleLabel,
        public string $employmentType,
        public ?string $employmentTypeLabel,
        public float $basicMonthlySalary,
        public ?string $hiredAt,
        public bool $isActive,
        public string $fullName,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Employee $employee, array $context = []): self
    {
        return new self(
            id: $employee->id,
            firstName: $employee->first_name,
            lastName: $employee->last_name,
            employeeNumber: $employee->employee_number,
            email: $employee->email,
            phone: $employee->phone,
            userId: $employee->user_id === null ? null : $employee->user_id,
            userEmail: $employee->user?->email,
            roleTitle: (string) $employee->role_title,
            roleTitleLabel: self::option(['Head Baker' => 'Head Baker', 'Assistant Baker' => 'Assistant Baker', 'Cashier' => 'Cashier', 'Pastry Staff' => 'Pastry Staff', 'Delivery Rider' => 'Delivery Rider', 'Manager' => 'Manager'], $employee->role_title),
            employmentType: (string) $employee->employment_type,
            employmentTypeLabel: self::option(['full_time' => 'Full-time regular', 'part_time' => 'Part-time flex'], $employee->employment_type),
            basicMonthlySalary: (float) $employee->basic_monthly_salary,
            hiredAt: self::date($employee->hired_at),
            isActive: (bool) $employee->is_active,
            fullName: $employee->full_name,
            createdAt: $employee->created_at?->toIso8601String(),
            updatedAt: $employee->updated_at?->toIso8601String(),
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
