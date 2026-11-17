<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Models\Coupon;
use Illuminate\Support\Facades\DB;

final class DeleteCouponAction
{
    public function execute(Coupon $coupon): void
    {
        DB::transaction(fn () => $coupon->delete());
    }
}
