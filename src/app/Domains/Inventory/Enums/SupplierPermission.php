<?php

namespace App\Domains\Inventory\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum SupplierPermission: string implements DefinesPermissions
{
    case SuppliersView = 'suppliers-view';
    case SuppliersCreate = 'suppliers-create';
    case SuppliersEdit = 'suppliers-edit';
    case SuppliersDelete = 'suppliers-delete';

    public function group(): string
    {
        return 'Inventory';
    }

    public function description(): string
    {
        return match ($this) {
            self::SuppliersView => 'View suppliers',
            self::SuppliersCreate => 'Create suppliers',
            self::SuppliersEdit => 'Edit suppliers',
            self::SuppliersDelete => 'Delete suppliers',
        };
    }
}
