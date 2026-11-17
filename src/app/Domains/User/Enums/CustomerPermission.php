<?php

namespace App\Domains\User\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum CustomerPermission: string implements DefinesPermissions
{
    case CustomersView = 'customers-view';
    case CustomersEdit = 'customers-edit';

    public function group(): string
    {
        return 'Orders & Sales';
    }

    public function description(): string
    {
        return match ($this) {
            self::CustomersView => 'View customers',
            self::CustomersEdit => 'Edit customers',
        };
    }
}
