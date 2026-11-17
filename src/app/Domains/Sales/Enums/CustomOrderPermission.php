<?php

namespace App\Domains\Sales\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum CustomOrderPermission: string implements DefinesPermissions
{
    case CustomOrdersView = 'custom-orders-view';
    case CustomOrdersCreate = 'custom-orders-create';
    case CustomOrdersEdit = 'custom-orders-edit';
    case CustomOrdersDelete = 'custom-orders-delete';

    public function group(): string
    {
        return 'Orders & Sales';
    }

    public function description(): string
    {
        return match ($this) {
            self::CustomOrdersView => 'View custom orders',
            self::CustomOrdersCreate => 'Create custom orders',
            self::CustomOrdersEdit => 'Edit custom orders',
            self::CustomOrdersDelete => 'Delete custom orders',
        };
    }
}
