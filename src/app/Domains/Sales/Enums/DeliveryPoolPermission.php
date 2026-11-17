<?php

namespace App\Domains\Sales\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum DeliveryPoolPermission: string implements DefinesPermissions
{
    case DeliveryPoolsView = 'delivery-pools-view';
    case DeliveryPoolsCreate = 'delivery-pools-create';
    case DeliveryPoolsEdit = 'delivery-pools-edit';
    case DeliveryPoolsDelete = 'delivery-pools-delete';

    public function group(): string
    {
        return 'Orders & Sales';
    }

    public function description(): string
    {
        return match ($this) {
            self::DeliveryPoolsView => 'View delivery pools',
            self::DeliveryPoolsCreate => 'Create delivery pools',
            self::DeliveryPoolsEdit => 'Edit delivery pools',
            self::DeliveryPoolsDelete => 'Delete delivery pools',
        };
    }
}
