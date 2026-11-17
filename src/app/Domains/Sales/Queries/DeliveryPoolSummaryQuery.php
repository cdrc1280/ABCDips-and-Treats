<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\Order;
use App\Shared\Resources\StatData;

final class DeliveryPoolSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        return [
            new StatData('waiting', __('Orders waiting for a pool'), Order::query()->where('delivery_mode', 'pooling')->whereNull('delivery_pool_id')->whereNotIn('status', ['cancelled', 'refunded', 'archived'])->count(), __('Pooling orders not assigned yet')),
            new StatData('open', __('Open pools'), DeliveryPool::query()->whereIn('status', ['open', 'building'])->count(), __('Not settled')),
            new StatData('settled', __('Settled'), DeliveryPool::query()->where('status', 'settled')->count(), __('Fees finalised')),
        ];
    }
}
