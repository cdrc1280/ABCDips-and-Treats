<?php

namespace App\Domains\Employee\Actions;

use App\Domains\Employee\Data\EmployeeData;
use App\Domains\Employee\Models\Employee;
use Illuminate\Support\Facades\DB;

final class CreateEmployeeAction
{
    public function execute(EmployeeData $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $employee = Employee::query()->create([
                'first_name' => $data->firstName,
                'last_name' => $data->lastName,
                'employee_number' => $data->employeeNumber,
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
