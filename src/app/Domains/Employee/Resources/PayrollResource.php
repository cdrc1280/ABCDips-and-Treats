<?php

namespace App\Domains\Employee\Resources;

use App\Domains\Employee\Models\Payroll;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class PayrollResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $payrollNumber,
        public ?string $periodStart,
        public ?string $periodEnd,
        public string $status,
        public ?string $statusLabel,
        public ?string $notes,
        public array $items,
        public float $totalGross,
        public float $totalDeductions,
        public float $totalNet,
        public int $itemsCount,
        public ?string $paidAt,
        public bool $isPaid,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Payroll $payroll, array $context = []): self
    {
        return new self(
            id: $payroll->id,
            payrollNumber: $payroll->payroll_number,
            periodStart: self::date($payroll->period_start),
            periodEnd: self::date($payroll->period_end),
            status: (string) $payroll->status,
            statusLabel: self::option(['draft' => 'Draft', 'approved' => 'Approved'], $payroll->status),
            notes: $payroll->notes,
            items: $payroll->items->map(fn ($row) => ['id' => $row->id, 'employee_id' => $row->employee_id, 'basic_pay' => $row->basic_pay, 'overtime_pay' => $row->overtime_pay, 'holiday_pay' => $row->holiday_pay, 'bonuses' => $row->bonuses, 'attendance_deduction' => $row->attendance_deduction, 'attendance_note' => $row->attendance_note, 'sss_deduction' => $row->sss_deduction, 'philhealth_deduction' => $row->philhealth_deduction, 'pagibig_deduction' => $row->pagibig_deduction, 'withholding_tax' => $row->withholding_tax, 'employee_name' => $row->employee?->full_name, 'net_pay' => (float) $row->net_pay])->values()->all(),
            totalGross: (float) $payroll->total_gross,
            totalDeductions: (float) $payroll->total_deductions,
            totalNet: (float) $payroll->total_net,
            itemsCount: (int) ($payroll->items_count ?? $payroll->items()->count()),
            paidAt: $payroll->paid_at?->toIso8601String(),
            isPaid: $payroll->status === 'paid',
            createdAt: $payroll->created_at?->toIso8601String(),
            updatedAt: $payroll->updated_at?->toIso8601String(),
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
