<?php

namespace App\Domains\Review\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum ReviewPermission: string implements DefinesPermissions
{
    case ReviewsView = 'reviews-view';
    case ReviewsCreate = 'reviews-create';
    case ReviewsEdit = 'reviews-edit';
    case ReviewsDelete = 'reviews-delete';

    public function group(): string
    {
        return 'Orders & Sales';
    }

    public function description(): string
    {
        return match ($this) {
            self::ReviewsView => 'View reviews',
            self::ReviewsCreate => 'Create reviews',
            self::ReviewsEdit => 'Edit reviews',
            self::ReviewsDelete => 'Delete reviews',
        };
    }
}
