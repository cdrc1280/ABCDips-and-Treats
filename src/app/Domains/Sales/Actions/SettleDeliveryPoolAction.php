<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\Order;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/** Settle pool. */
final class SettleDeliveryPoolAction
{
    public function execute(DeliveryPool $deliveryPool): DeliveryPool
    {
        if (in_array($deliveryPool->status, ['settled', 'dispatched'], true)) {
            throw new DomainRuleViolation(__('This pool is already settled.'));
        }
        if (! $deliveryPool->orders()->exists()) {
            throw new DomainRuleViolation(__('Add at least one order to the pool before settling it.'));
        }

        DB::transaction(function () use ($deliveryPool) {
            $orders = $deliveryPool->orders()->get();
            foreach ($orders as $order) {
                $fee = (float) $order->delivery_fee;
                $order->update([
                    'total' => max(0.0, round((float) $order->subtotal - (float) $order->discount_amount + $fee, 2)),
                    'pooling_status' => Order::POOLING_SETTLED,
                ]);
                if ($order->status === Order::STATUS_PENDING) {
                    $order->transitionTo(Order::STATUS_CONFIRMED, __('Delivery pool :code settled — shipping share ₱:fee.', ['code' => $deliveryPool->pool_code, 'fee' => number_format($fee, 2)]), auth()->id() === null ? null : (string) auth()->id());
                }
            }
            $deliveryPool->update([
                'shared_fee_per_order' => round($orders->avg('delivery_fee') ?? 0, 2),
                'status' => 'settled',
                'settled_at' => now(),
            ]);
        });

        return $deliveryPool->refresh();
    }
}
