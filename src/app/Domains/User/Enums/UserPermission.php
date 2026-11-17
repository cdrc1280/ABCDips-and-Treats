<?php

namespace App\Domains\User\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum UserPermission: string implements DefinesPermissions
{
    case UsersView = 'users-view';
    case UsersCreate = 'users-create';
    case UsersEdit = 'users-edit';
    case UsersDelete = 'users-delete';

    public function group(): string
    {
        return 'Access';
    }

    public function description(): string
    {
        return match ($this) {
            self::UsersView => 'View staff accounts',
            self::UsersCreate => 'Create staff accounts',
            self::UsersEdit => 'Edit staff accounts',
            self::UsersDelete => 'Delete staff accounts',
        };
    }
}
