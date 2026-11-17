<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Data\EmployeeData;
use App\Domains\Employee\Models\Employee;
use Illuminate\Support\Facades\DB;

final class UpdateEmployeeAction
{
    public function execute(Employee $employee, EmployeeData $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $employee->update([
                'first_name' => $data->firstName,
                'last_name' => $data->lastName,
                'employee_number' => $data->employeeNumber ?? $employee->employee_number,
                'email' => $data->email,
                'phone' => $data->phone,
                'user_id' => $data->userId,
                'role_title' => $data->roleTitle,
                'employment_type' => $data->employmentType,
                'basic_monthly_salary' => $data->basicMonthlySalary,
                'hired_at' => $data->hiredAt,
                'is_active' => $data->isActive,
            ]);

            return $employee;
        });
    }
}
