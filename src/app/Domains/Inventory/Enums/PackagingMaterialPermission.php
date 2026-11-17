<?php

namespace App\Domains\Inventory\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum PackagingMaterialPermission: string implements DefinesPermissions
{
    case PackagingMaterialsView = 'packaging-materials-view';
    case PackagingMaterialsCreate = 'packaging-materials-create';
    case PackagingMaterialsEdit = 'packaging-materials-edit';
    case PackagingMaterialsDelete = 'packaging-materials-delete';

    public function group(): string
    {
        return 'Inventory';
    }

    public function description(): string
    {
        return match ($this) {
            self::PackagingMaterialsView => 'View packaging',
            self::PackagingMaterialsCreate => 'Create packaging',
            self::PackagingMaterialsEdit => 'Edit packaging',
            self::PackagingMaterialsDelete => 'Delete packaging',
        };
    }
}
