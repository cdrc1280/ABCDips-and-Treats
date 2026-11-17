<?php

namespace App\Domains\Catalog\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum CategoryPermission: string implements DefinesPermissions
{
    case CategoriesView = 'categories-view';
    case CategoriesCreate = 'categories-create';
    case CategoriesEdit = 'categories-edit';
    case CategoriesDelete = 'categories-delete';

    public function group(): string
    {
        return 'Catalog';
    }

    public function description(): string
    {
        return match ($this) {
            self::CategoriesView => 'View product categories',
            self::CategoriesCreate => 'Create product categories',
            self::CategoriesEdit => 'Edit product categories',
            self::CategoriesDelete => 'Delete product categories',
        };
    }
}
