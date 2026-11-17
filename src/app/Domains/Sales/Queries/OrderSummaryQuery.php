<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\Order;
use App\Shared\Resources\StatData;

final class OrderSummaryQuery
{
    /**
     * @return list<StatData>
     */
    public function execute(): array
    {
        $today = (float) Order::query()->whereDate('created_at', today())->where('payment_status', 'paid')->sum('total');

        return [
            new StatData('today', __('Orders today'), Order::query()->whereDate('created_at', today())->count(), __('All channels')),
            new StatData('open', __('In progress'), Order::query()->whereIn('status', ['pending', 'confirmed', 'preparing', 'packaging', 'out_for_delivery', 'ready_for_pickup'])->count(), __('Not completed yet')),
            new StatData('awaiting', __('Awaiting payment'), Order::query()->whereIn('payment_status', ['pending', 'awaiting_payment'])->whereNotIn('status', ['cancelled', 'refunded', 'archived'])->count(), __('Unpaid, still active')),
            new StatData('revenue_today', __('Sales today'), $today, __('Paid orders placed today'), '₱'.number_format($today, 2)),
        ];
    }
}
