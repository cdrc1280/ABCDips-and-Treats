<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Data\PayrollData;
use App\Domains\Employee\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Support\Facades\DB;

final class CreatePayrollAction
{
    public function execute(PayrollData $data): Payroll
    {
        return DB::transaction(function () use ($data) {
            $payroll = app(PayrollService::class)->generatePayroll($data->periodStart, $data->periodEnd);
            $payroll->update(['notes' => $data->notes]);

            return $payroll;
        });
    }
}
