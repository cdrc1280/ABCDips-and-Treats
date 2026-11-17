<?php

namespace App\Domains\Costing\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum ProductCostingPermission: string implements DefinesPermissions
{
    case ProductCostingsView = 'product-costings-view';
    case ProductCostingsCreate = 'product-costings-create';
    case ProductCostingsEdit = 'product-costings-edit';
    case ProductCostingsDelete = 'product-costings-delete';

    public function group(): string
    {
        return 'Catalog';
    }

    public function description(): string
    {
        return match ($this) {
            self::ProductCostingsView => 'View product costing',
            self::ProductCostingsCreate => 'Create product costing',
            self::ProductCostingsEdit => 'Edit product costing',
            self::ProductCostingsDelete => 'Delete product costing',
        };
    }
}
