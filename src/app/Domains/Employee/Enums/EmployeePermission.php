<?php

namespace App\Domains\Employee\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum EmployeePermission: string implements DefinesPermissions
{
    case EmployeesView = 'employees-view';
    case EmployeesCreate = 'employees-create';
    case EmployeesEdit = 'employees-edit';
    case EmployeesDelete = 'employees-delete';

    public function group(): string
    {
        return 'HR & Payroll';
    }

    public function description(): string
    {
        return match ($this) {
            self::EmployeesView => 'View employees',
            self::EmployeesCreate => 'Create employees',
            self::EmployeesEdit => 'Edit employees',
            self::EmployeesDelete => 'Delete employees',
        };
    }
}
