<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Data\PayrollData;
use App\Domains\Employee\Models\Payroll;
use App\Domains\Employee\Support\PayLine;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class UpdatePayrollAction
{
    public function execute(Payroll $payroll, PayrollData $data): Payroll
    {
        if ($payroll->status === 'paid') {
            throw new DomainRuleViolation(__('Paid payroll is locked.'));
        }

        return DB::transaction(function () use ($payroll, $data) {
            $payroll->update([
                'payroll_number' => $data->payrollNumber ?? $payroll->payroll_number,
                'period_start' => $data->periodStart,
                'period_end' => $data->periodEnd,
                'status' => $data->status,
                'notes' => $data->notes,
            ]);
            $payroll->items()->delete();
            foreach ($data->items as $row) {
                $payroll->items()->create([
                    'employee_id' => (string) $row['employee_id'],
                    ...PayLine::totals($row),
                ]);
            }
            $payroll->update(['total_gross' => round((float) $payroll->items()->sum('gross_pay'), 2), 'total_deductions' => round((float) $payroll->items()->sum('total_deductions'), 2), 'total_net' => round((float) $payroll->items()->sum('net_pay'), 2)]);

            return $payroll;
        });
    }
}
