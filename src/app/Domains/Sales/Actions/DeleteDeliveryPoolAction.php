<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Models\DeliveryPool;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteDeliveryPoolAction
{
    public function execute(DeliveryPool $deliveryPool): void
    {
        if ($deliveryPool->orders()->exists()) {
            throw new DomainRuleViolation(__('Remove the orders from this pool before deleting it.'));
        }

        DB::transaction(fn () => $deliveryPool->delete());
    }
}
