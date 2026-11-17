<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Models\Order;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/** Change status. */
final class ChangeStatusOrderAction
{
    public function execute(Order $order, mixed $value): Order
    {
        if ($value === $order->status) {
            throw new DomainRuleViolation(__('The order is already :status.', ['status' => $value]));
        }

        DB::transaction(function () use ($order, $value) {
            try {
                $order->transitionTo((string) $value, __('Updated from the back office'), auth()->id() === null ? null : (string) auth()->id());
            } catch (\DomainException $e) {
                throw new DomainRuleViolation($e->getMessage());
            }
        });

        return $order->refresh();
    }
}
