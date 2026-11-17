<?php

namespace App\Domains\Sales\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum OrderPermission: string implements DefinesPermissions
{
    case OrdersView = 'orders-view';
    case OrdersEdit = 'orders-edit';

    public function group(): string
    {
        return 'Orders & Sales';
    }

    public function description(): string
    {
        return match ($this) {
            self::OrdersView => 'View orders',
            self::OrdersEdit => 'Edit orders',
        };
    }
}
