<?php

namespace App\Domains\Employee\Support;

use App\Domains\Setting\Models\Setting;

/**
 * The shop's pay policy (audit A16), read from Store settings → "Payroll rates".
 * Defaults follow the Philippine Labor Code minimums; the owner can change every value.
 */
final class PayPolicy
{
    public function __construct(
        /** Daily rate = monthly salary × 12 ÷ this (313 = 6-day week, 261 = 5-day week). */
        public readonly float $workDaysPerYear = 313,
        public readonly float $hoursPerDay = 8,
        /** Overtime premium on top of the hourly rate, % (Labor Code Art. 87: 25). */
        public readonly float $overtimePremium = 25,
        /** Extra pay for working a holiday, % of the daily rate (regular holiday, Art. 94: 100). */
        public readonly float $holidayPremium = 100,
        public readonly bool $deductAbsences = true,
        public readonly bool $deductLate = true,
        /** Scheduled shift start, "HH:MM"; lateness counts from here. */
        public readonly string $shiftStart = '08:00',
        public readonly int $lateGraceMinutes = 0,
        /** Statutory contribution and tax rates, per semi-monthly cut-off. */
        public readonly float $sssRate = 4.5,
        public readonly float $sssCap = 1350,
        public readonly float $philhealthRate = 2.5,
        public readonly float $pagibigAmount = 100,
        public readonly float $taxThreshold = 10416,
        public readonly float $taxRate = 15,
    ) {}

    public static function fromSettings(): self
    {
        $num = fn (string $key, float $default): float => is_numeric($value = Setting::get("payroll_{$key}")) ? (float) $value : $default;
        $bool = fn (string $key, bool $default): bool => ($value = Setting::get("payroll_{$key}")) === null ? $default : in_array((string) $value, ['1', 'true'], true);
        $shift = (string) Setting::get('payroll_shift_start', '08:00');

        return new self(
            workDaysPerYear: max(1.0, $num('work_days_per_year', 313)),
            hoursPerDay: max(1.0, $num('hours_per_day', 8)),
            overtimePremium: max(0.0, $num('overtime_premium', 25)),
            holidayPremium: max(0.0, $num('holiday_premium', 100)),
            deductAbsences: $bool('deduct_absences', true),
            deductLate: $bool('deduct_late', true),
            shiftStart: preg_match('/^\d{1,2}:\d{2}/', $shift) ? $shift : '08:00',
            lateGraceMinutes: max(0, (int) $num('late_grace_minutes', 0)),
            sssRate: $num('sss_rate', 5),
            sssCap: $num('sss_cap', 875),
            philhealthRate: $num('philhealth_rate', 2.5),
            pagibigAmount: $num('pagibig_amount', 100),
            taxThreshold: $num('tax_threshold', 10416),
            taxRate: $num('tax_rate', 15),
        );
    }

    public function dailyRate(float $monthlySalary): float
    {
        return $monthlySalary * 12 / $this->workDaysPerYear;
    }

    public function hourlyRate(float $monthlySalary): float
    {
        return $this->dailyRate($monthlySalary) / $this->hoursPerDay;
    }
}
