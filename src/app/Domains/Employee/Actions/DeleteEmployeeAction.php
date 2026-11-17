<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Models\Employee;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteEmployeeAction
{
    public function execute(Employee $employee): void
    {
        if ($employee->payrollItems()->exists()) {
            throw new DomainRuleViolation(__(':name has payroll history. Mark them inactive instead.', ['name' => $employee->full_name]));
        }

        DB::transaction(fn () => $employee->delete());
    }
}
