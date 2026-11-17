<?php

namespace App\Domains\Employee\Data;

use App\Rules\PhilippinePhone;
use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class EmployeeData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $hiredAt,
        public ?string $employeeNumber = null,
        public ?string $phone = null,
        public ?string $userId = null,
        public ?string $roleTitle = 'Pastry Staff',
        public ?string $employmentType = 'full_time',
        public ?float $basicMonthlySalary = 20000,
        public bool $isActive = true,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['first_name'] = trim((string) ($properties['first_name'] ?? '')) ?: null;
        $properties['last_name'] = trim((string) ($properties['last_name'] ?? '')) ?: null;
        $properties['email'] = strtolower(trim((string) ($properties['email'] ?? ''))) ?: null;
        $properties['hired_at'] = trim((string) ($properties['hired_at'] ?? '')) ?: null;
        $properties['employee_number'] = strtoupper(trim((string) ($properties['employee_number'] ?? ''))) ?: null;
        $properties['phone'] = trim((string) ($properties['phone'] ?? '')) ?: null;
        $properties['user_id'] = ($properties['user_id'] ?? '') === '' ? null : (string) $properties['user_id'];
        $properties['role_title'] = trim((string) ($properties['role_title'] ?? '')) ?: 'Pastry Staff';
        $properties['employment_type'] = trim((string) ($properties['employment_type'] ?? '')) ?: 'full_time';
        $properties['basic_monthly_salary'] = ($properties['basic_monthly_salary'] ?? '') === '' ? 20000 : (float) $properties['basic_monthly_salary'];
        $properties['is_active'] = filter_var($properties['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('employee');

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'hired_at' => ['required', 'date'],
            'employee_number' => ['nullable', 'string', 'max:30', Rule::unique('employees', 'employee_number')->ignore($id)->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', new PhilippinePhone],
            'user_id' => ['bail', 'nullable', 'uuid', Rule::exists('users', 'id')],
            'role_title' => ['required', Rule::in(['Head Baker', 'Assistant Baker', 'Cashier', 'Pastry Staff', 'Delivery Rider', 'Manager'])],
            'employment_type' => ['required', Rule::in(['full_time', 'part_time'])],
            'basic_monthly_salary' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [

        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'first_name' => __('first name'),
            'last_name' => __('last name'),
            'employee_number' => __('employee number'),
            'email' => __('email'),
            'phone' => __('phone'),
            'user_id' => __('login account'),
            'role_title' => __('role'),
            'employment_type' => __('employment type'),
            'basic_monthly_salary' => __('basic monthly salary'),
            'hired_at' => __('hired on'),
            'is_active' => __('active'),
        ];
    }
}
