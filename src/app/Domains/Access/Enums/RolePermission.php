<?php

namespace App\Domains\Access\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum RolePermission: string implements DefinesPermissions
{
    case RolesView = 'roles-view';
    case RolesCreate = 'roles-create';
    case RolesEdit = 'roles-edit';
    case RolesDelete = 'roles-delete';

    public function group(): string
    {
        return 'Access';
    }

    public function description(): string
    {
        return match ($this) {
            self::RolesView => 'View roles',
            self::RolesCreate => 'Create roles',
            self::RolesEdit => 'Edit roles',
            self::RolesDelete => 'Delete roles',
        };
    }
}
