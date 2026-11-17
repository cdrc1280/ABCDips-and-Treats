<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Models\Payroll;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/** Approve. */
final class ApprovePayrollAction
{
    public function execute(Payroll $payroll): Payroll
    {
        if ($payroll->status !== 'draft') {
            throw new DomainRuleViolation(__('Only draft payroll can be approved.'));
        }

        DB::transaction(function () use ($payroll) {
            $payroll->update(['status' => 'approved']);
        });

        return $payroll->refresh();
    }
}
