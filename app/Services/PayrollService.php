<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Support\Str;

class PayrollService
{
    public function generatePayroll(string $periodStart, string $periodEnd, ?array $employeeIds = null): Payroll
    {
        $payrollNumber = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $payroll = Payroll::create([
            'payroll_number' => $payrollNumber,
            'period_start'   => $periodStart,
            'period_end'     => $periodEnd,
            'status'         => Payroll::STATUS_DRAFT,
        ]);

        $query = Employee::where('is_active', true);
        if (!empty($employeeIds)) {
            $query->whereIn('id', $employeeIds);
        }
        $employees = $query->get();

        $totalGross = 0.0;
        $totalDeductions = 0.0;
        $totalNet = 0.0;

        foreach ($employees as $emp) {
            $basic = (float) $emp->basic_monthly_salary / 2; // Semi-monthly payroll (15-day cycle)
            $overtime = 0.0;
            $bonuses = 0.0;
            $gross = $basic + $overtime + $bonuses;

            // Philippine Statutory Deductions (Semi-monthly half rates)
            $sss = min(1350.00, round($gross * 0.045, 2));
            $philhealth = round($gross * 0.025, 2);
            $pagibig = 100.00;
            $tax = $gross <= 10416.00 ? 0.00 : round(($gross - 10416.00) * 0.15, 2);

            $itemDeductions = round($sss + $philhealth + $pagibig + $tax, 2);
            $net = max(0.00, round($gross - $itemDeductions, 2));

            PayrollItem::create([
                'payroll_id'           => $payroll->id,
                'employee_id'          => $emp->id,
                'basic_pay'            => $basic,
                'overtime_pay'         => $overtime,
                'bonuses'              => $bonuses,
                'sss_deduction'        => $sss,
                'philhealth_deduction' => $philhealth,
                'pagibig_deduction'    => $pagibig,
                'withholding_tax'     => $tax,
                'gross_pay'            => $gross,
                'total_deductions'     => $itemDeductions,
                'net_pay'              => $net,
            ]);

            $totalGross += $gross;
            $totalDeductions += $itemDeductions;
            $totalNet += $net;
        }

        $payroll->update([
            'total_gross'      => round($totalGross, 2),
            'total_deductions' => round($totalDeductions, 2),
            'total_net'        => round($totalNet, 2),
        ]);

        return $payroll->fresh(['items.employee']);
    }

    public function approveAndPay(Payroll $payroll): Payroll
    {
        $payroll->update([
            'status'  => Payroll::STATUS_PAID,
            'paid_at' => now(),
        ]);

        return $payroll->fresh();
    }
}
