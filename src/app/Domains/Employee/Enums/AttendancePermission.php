<?php

namespace App\Domains\Employee\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum AttendancePermission: string implements DefinesPermissions
{
    case AttendancesView = 'attendances-view';
    case AttendancesCreate = 'attendances-create';
    case AttendancesEdit = 'attendances-edit';
    case AttendancesDelete = 'attendances-delete';

    public function group(): string
    {
        return 'HR & Payroll';
    }

    public function description(): string
    {
        return match ($this) {
            self::AttendancesView => 'View attendance',
            self::AttendancesCreate => 'Create attendance',
            self::AttendancesEdit => 'Edit attendance',
            self::AttendancesDelete => 'Delete attendance',
        };
    }
}
