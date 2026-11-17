<?php

namespace App\Domains\Inventory\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum IngredientPermission: string implements DefinesPermissions
{
    case IngredientsView = 'ingredients-view';
    case IngredientsCreate = 'ingredients-create';
    case IngredientsEdit = 'ingredients-edit';
    case IngredientsDelete = 'ingredients-delete';

    public function group(): string
    {
        return 'Inventory';
    }

    public function description(): string
    {
        return match ($this) {
            self::IngredientsView => 'View ingredients',
            self::IngredientsCreate => 'Create ingredients',
            self::IngredientsEdit => 'Edit ingredients',
            self::IngredientsDelete => 'Delete ingredients',
        };
    }
}
