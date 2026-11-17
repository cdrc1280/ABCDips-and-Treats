<?php

namespace App\Services;

use App\Domains\Employee\Models\Attendance;
use App\Domains\Employee\Models\Employee;
use App\Domains\Employee\Models\Payroll;
use App\Domains\Employee\Models\PayrollItem;
use App\Domains\Employee\Support\PayLine;
use App\Domains\Employee\Support\PayPolicy;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PayrollService
{
    public function generatePayroll(string $periodStart, string $periodEnd, ?array $employeeIds = null): Payroll
    {
        $payrollNumber = 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(4));

        $payroll = Payroll::create([
            'payroll_number' => $payrollNumber,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'status' => Payroll::STATUS_DRAFT,
        ]);

        $query = Employee::where('is_active', true);
        if (! empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        }
        $employees = $query->get();

        $policy = PayPolicy::fromSettings();
        $attendance = Attendance::query()
            ->whereIn('employee_id', $employees->pluck('id'))
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->get()
            ->groupBy('employee_id');

        foreach ($employees as $emp) {
            $monthly = (float) $emp->basic_monthly_salary;
            $basic = round($monthly / 2, 2); // Semi-monthly payroll (15-day cycle)
            $worked = $this->attendancePay($monthly, $attendance->get($emp->id, collect()), $policy);

            // Contributions and tax apply to what was actually earned (after absences/lateness).
            // Rates come from Store settings → Payroll rates, so the owner can update them when the law changes.
            $gross = max(0.0, $basic - $worked['attendance_deduction']) + $worked['overtime_pay'] + $worked['holiday_pay'];
            $sss = min($policy->sssCap, round($gross * $policy->sssRate / 100, 2));
            $philhealth = round($gross * $policy->philhealthRate / 100, 2);
            $tax = $gross <= $policy->taxThreshold ? 0.00 : round(($gross - $policy->taxThreshold) * $policy->taxRate / 100, 2);

            PayrollItem::create([
                'payroll_id' => $payroll->id,
                'employee_id' => $emp->id,
                ...PayLine::totals([
                    'basic_pay' => $basic,
                    'overtime_pay' => $worked['overtime_pay'],
                    'holiday_pay' => $worked['holiday_pay'],
                    'bonuses' => 0,
                    'attendance_deduction' => $worked['attendance_deduction'],
                    'attendance_note' => $worked['note'],
                    'sss_deduction' => $sss,
                    'philhealth_deduction' => $philhealth,
                    'pagibig_deduction' => $policy->pagibigAmount,
                    'withholding_tax' => $tax,
                ]),
            ]);
        }

        $payroll->update([
            'total_gross' => round((float) $payroll->items()->sum('gross_pay'), 2),
            'total_deductions' => round((float) $payroll->items()->sum('total_deductions'), 2),
            'total_net' => round((float) $payroll->items()->sum('net_pay'), 2),
        ]);

        return $payroll->fresh(['items.employee']);
    }

    /**
     * Overtime, holiday premium and absence/late deductions from the period's attendance (audit A16).
     * Only recorded days count: a day with no attendance row is neither paid extra nor deducted.
     *
     * @param  Collection<int, Attendance>  $days
     * @return array{overtime_pay: float, holiday_pay: float, attendance_deduction: float, note: ?string}
     */
    public function attendancePay(float $monthlySalary, Collection $days, PayPolicy $policy): array
    {
        $daily = $policy->dailyRate($monthlySalary);
        $hourly = $policy->hourlyRate($monthlySalary);
        $shiftStart = $this->minutesOfDay($policy->shiftStart) ?? 8 * 60;

        $absentDays = 0.0;
        $lateMinutes = 0;
        $overtimeHours = 0.0;
        $holidayDays = 0.0;

        foreach ($days as $day) {
            $worked = in_array($day->status, ['present', 'late', 'half_day'], true);

            if ($day->status === 'absent') {
                $absentDays += 1;
            } elseif ($day->status === 'half_day') {
                $absentDays += 0.5;
            }
            if ($day->status === 'late' && ($in = $this->minutesOfDay((string) $day->time_in)) !== null) {
                $lateMinutes += max(0, $in - $shiftStart - $policy->lateGraceMinutes);
            }
            if ($worked && (float) $day->overtime_hours > 0) {
                $overtimeHours += (float) $day->overtime_hours;
            }
            if ($worked && $day->is_holiday) {
                $holidayDays += $day->status === 'half_day' ? 0.5 : 1.0;
            }
        }

        $deduction = ($policy->deductAbsences ? $absentDays * $daily : 0.0) + ($policy->deductLate ? $lateMinutes * $hourly / 60 : 0.0);

        $parts = array_filter([
            $absentDays > 0 ? $this->count($absentDays, 'day absent', 'days absent') : null,
            $lateMinutes > 0 ? "{$lateMinutes} min late" : null,
            $overtimeHours > 0 ? $this->count($overtimeHours, 'h overtime', 'h overtime') : null,
            $holidayDays > 0 ? $this->count($holidayDays, 'holiday worked', 'holidays worked') : null,
        ]);

        return [
            'overtime_pay' => round($overtimeHours * $hourly * (1 + $policy->overtimePremium / 100), 2),
            'holiday_pay' => round($holidayDays * $daily * $policy->holidayPremium / 100, 2),
            'attendance_deduction' => round($deduction, 2),
            'note' => $parts === [] ? null : implode(' · ', $parts),
        ];
    }

    private function minutesOfDay(string $time): ?int
    {
        return preg_match('/^(\d{1,2}):(\d{2})/', $time, $m) ? ((int) $m[1]) * 60 + (int) $m[2] : null;
    }

    private function count(float $value, string $one, string $many): string
    {
        $shown = rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');

        return $shown.' '.($value == 1.0 ? $one : $many);
    }

    public function approveAndPay(Payroll $payroll): Payroll
    {
        $payroll->update([
            'status' => Payroll::STATUS_PAID,
            'paid_at' => now(),
        ]);

        return $payroll->fresh();
    }
}
