<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Data\CouponData;
use App\Domains\Sales\Models\Coupon;
use Illuminate\Support\Facades\DB;

final class UpdateCouponAction
{
    public function execute(Coupon $coupon, CouponData $data): Coupon
    {
        return DB::transaction(function () use ($coupon, $data) {
            $coupon->update([
                'code' => $data->code,
                'type' => $data->type,
                'value' => $data->value,
                'min_spend' => $data->minSpend,
                'max_discount' => $data->maxDiscount,
                'usage_limit' => $data->usageLimit,
                'starts_at' => $data->startsAt,
                'expires_at' => $data->expiresAt,
                'is_active' => $data->isActive,
            ]);

            return $coupon;
        });
    }
}
