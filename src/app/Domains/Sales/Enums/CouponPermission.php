<?php

namespace App\Domains\Sales\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum CouponPermission: string implements DefinesPermissions
{
    case CouponsView = 'coupons-view';
    case CouponsCreate = 'coupons-create';
    case CouponsEdit = 'coupons-edit';
    case CouponsDelete = 'coupons-delete';

    public function group(): string
    {
        return 'Orders & Sales';
    }

    public function description(): string
    {
        return match ($this) {
            self::CouponsView => 'View coupons',
            self::CouponsCreate => 'Create coupons',
            self::CouponsEdit => 'Edit coupons',
            self::CouponsDelete => 'Delete coupons',
        };
    }
}
