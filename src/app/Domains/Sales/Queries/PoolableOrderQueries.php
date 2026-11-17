<?php

namespace App\Domains\Sales\Queries;

use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\Order;

/**
 * Orders that can be put in a delivery pool: pooling-mode orders not yet in another pool
 * (plus, when editing, the ones already in this pool).
 */
final class PoolableOrderQueries
{
    /**
     * @return array<int, array{label: string, value: string, description: string}>
     */
    public function options(?DeliveryPool $pool = null): array
    {
        return Order::query()
            ->where('delivery_mode', Order::MODE_POOLING)
            ->whereNotIn('status', [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED, Order::STATUS_ARCHIVED])
            ->where(fn ($q) => $q->whereNull('delivery_pool_id')->when($pool, fn ($q) => $q->orWhere('delivery_pool_id', $pool->id)))
            ->orderBy('created_at')
            ->get(['id', 'order_number', 'customer_name', 'city', 'barangay'])
            ->map(fn (Order $order) => [
                'label' => "{$order->order_number} — {$order->customer_name}",
                'value' => $order->id,
                'description' => trim(implode(', ', array_filter([$order->barangay, $order->city]))) ?: 'No address',
            ])
            ->values()->all();
    }
}
