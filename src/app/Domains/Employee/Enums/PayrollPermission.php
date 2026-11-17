<?php

namespace App\Domains\Employee\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum PayrollPermission: string implements DefinesPermissions
{
    case PayrollsView = 'payrolls-view';
    case PayrollsCreate = 'payrolls-create';
    case PayrollsEdit = 'payrolls-edit';
    case PayrollsDelete = 'payrolls-delete';

    public function group(): string
    {
        return 'HR & Payroll';
    }

    public function description(): string
    {
        return match ($this) {
            self::PayrollsView => 'View payroll',
            self::PayrollsCreate => 'Create payroll',
            self::PayrollsEdit => 'Edit payroll',
            self::PayrollsDelete => 'Delete payroll',
        };
    }
}
