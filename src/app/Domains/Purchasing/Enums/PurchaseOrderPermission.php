<?php

namespace App\Domains\Purchasing\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum PurchaseOrderPermission: string implements DefinesPermissions
{
    case PurchaseOrdersView = 'purchase-orders-view';
    case PurchaseOrdersCreate = 'purchase-orders-create';
    case PurchaseOrdersEdit = 'purchase-orders-edit';
    case PurchaseOrdersDelete = 'purchase-orders-delete';

    public function group(): string
    {
        return 'Production & Purchasing';
    }

    public function description(): string
    {
        return match ($this) {
            self::PurchaseOrdersView => 'View purchase orders',
            self::PurchaseOrdersCreate => 'Create purchase orders',
            self::PurchaseOrdersEdit => 'Edit purchase orders',
            self::PurchaseOrdersDelete => 'Delete purchase orders',
        };
    }
}
