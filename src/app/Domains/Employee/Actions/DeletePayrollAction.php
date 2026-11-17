<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Models\Payroll;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeletePayrollAction
{
    public function execute(Payroll $payroll): void
    {
        if ($payroll->status === 'paid') {
            throw new DomainRuleViolation(__('Paid payroll cannot be deleted.'));
        }

        DB::transaction(fn () => $payroll->delete());
    }
}
