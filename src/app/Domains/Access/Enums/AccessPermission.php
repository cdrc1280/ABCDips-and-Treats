<?php

namespace App\Domains\Access\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum AccessPermission: string implements DefinesPermissions
{
    case DashboardView = 'dashboard-view';
    case RolesView = 'roles-view';
    case RolesCreate = 'roles-create';
    case RolesEdit = 'roles-edit';
    case RolesDelete = 'roles-delete';
    case AuditLogsView = 'audit-logs-view';

    public function group(): string
    {
        return 'Access';
    }

    public function description(): string
    {
        return match ($this) {
            self::DashboardView => 'Open the back office and see the dashboard',
            self::RolesView => 'View roles and their permissions',
            self::RolesCreate => 'Create roles',
            self::RolesEdit => 'Edit roles and assign permissions',
            self::RolesDelete => 'Delete roles',
            self::AuditLogsView => 'View the activity / audit log',
        };
    }
}
