<?php

namespace App\Domains\Employee\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class PayrollData extends Data
{
    public function __construct(
        public string $periodStart,
        public string $periodEnd,
        public ?string $payrollNumber = null,
        public ?string $status = 'draft',
        public ?string $notes = null,
        public array $items = [],
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['period_start'] = trim((string) ($properties['period_start'] ?? '')) ?: null;
        $properties['period_end'] = trim((string) ($properties['period_end'] ?? '')) ?: null;
        $properties['payroll_number'] = strtoupper(trim((string) ($properties['payroll_number'] ?? ''))) ?: null;
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'draft';
        $properties['notes'] = trim((string) ($properties['notes'] ?? '')) ?: null;
        $properties['items'] = array_values(array_filter((array) ($properties['items'] ?? []), fn ($row) => is_array($row) && array_filter($row, fn ($v) => $v !== null && $v !== '') !== []));

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('payroll');

        return [
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'payroll_number' => ['nullable', 'string', 'max:40', Rule::unique('payrolls', 'payroll_number')->ignore($id)->whereNull('deleted_at')],
            'status' => ['required', Rule::in(['draft', 'approved'])],
            'notes' => ['nullable', 'string', 'max:5000'],
            'items' => ['array', 'max:100'],
            'items.*.employee_id' => ['bail', 'required', 'uuid', Rule::exists('employees', 'id')],
            'items.*.basic_pay' => ['required', 'numeric', 'min:0'],
            'items.*.overtime_pay' => ['nullable', 'numeric', 'min:0'],
            'items.*.holiday_pay' => ['nullable', 'numeric', 'min:0'],
            'items.*.bonuses' => ['nullable', 'numeric', 'min:0'],
            'items.*.attendance_deduction' => ['nullable', 'numeric', 'min:0'],
            'items.*.attendance_note' => ['nullable', 'string', 'max:255'],
            'items.*.sss_deduction' => ['nullable', 'numeric', 'min:0'],
            'items.*.philhealth_deduction' => ['nullable', 'numeric', 'min:0'],
            'items.*.pagibig_deduction' => ['nullable', 'numeric', 'min:0'],
            'items.*.withholding_tax' => ['nullable', 'numeric', 'min:0'],
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
            'payroll_number' => __('payroll number'),
            'period_start' => __('period start'),
            'period_end' => __('period end'),
            'status' => __('status'),
            'notes' => __('notes'),
            'items' => __('pay lines'),
        ];
    }
}
