<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Models\Payroll;
use App\Services\PayrollService;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/** Mark paid. */
final class MarkPaidPayrollAction
{
    public function execute(Payroll $payroll): Payroll
    {
        if ($payroll->status === 'paid') {
            throw new DomainRuleViolation(__('This payroll is already paid.'));
        }

        DB::transaction(function () use ($payroll) {
            app(PayrollService::class)->approveAndPay($payroll);
        });

        return $payroll->refresh();
    }
}
