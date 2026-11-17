<?php

namespace App\Domains\Catalog\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum ProductPermission: string implements DefinesPermissions
{
    case ProductsView = 'products-view';
    case ProductsCreate = 'products-create';
    case ProductsEdit = 'products-edit';
    case ProductsDelete = 'products-delete';

    public function group(): string
    {
        return 'Catalog';
    }

    public function description(): string
    {
        return match ($this) {
            self::ProductsView => 'View products',
            self::ProductsCreate => 'Create products',
            self::ProductsEdit => 'Edit products',
            self::ProductsDelete => 'Delete products',
        };
    }
}
