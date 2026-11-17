<?php

namespace App\Domains\Costing\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum RecipePermission: string implements DefinesPermissions
{
    case RecipesView = 'recipes-view';
    case RecipesCreate = 'recipes-create';
    case RecipesEdit = 'recipes-edit';
    case RecipesDelete = 'recipes-delete';

    public function group(): string
    {
        return 'Catalog';
    }

    public function description(): string
    {
        return match ($this) {
            self::RecipesView => 'View recipes',
            self::RecipesCreate => 'Create recipes',
            self::RecipesEdit => 'Edit recipes',
            self::RecipesDelete => 'Delete recipes',
        };
    }
}
