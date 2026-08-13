<?php

namespace App\Filament\Resources\DeliveryPoolResource\Pages;

use App\Filament\Resources\DeliveryPoolResource;
use App\Models\DeliveryPool;
use App\Models\Order;
use Filament\Resources\Pages\CreateRecord;

class CreateDeliveryPool extends CreateRecord
{
    protected static string $resource = DeliveryPoolResource::class;

    protected array $orderAllocations = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->orderAllocations = $data['order_allocations'] ?? [];
        $count = max(1, count($this->orderAllocations));
        $totalAllocated = array_sum(array_column($this->orderAllocations, 'assigned_fee'));
        $routeFee = (float) ($data['total_delivery_fee'] ?? 0);

        if ($totalAllocated < $routeFee && count($this->orderAllocations) > 0) {
            $remaining = number_format($routeFee - $totalAllocated, 2);
            \Filament\Notifications\Notification::make()
                ->title('Shipping Fee Shortfall')
                ->body("The total allocated fees (\u{20B1}" . number_format($totalAllocated, 2) . ") do not fully cover the route fee (\u{20B1}" . number_format($routeFee, 2) . "). Remaining: \u{20B1}{$remaining}. The pool will be saved but cannot be settled until fully covered.")
                ->warning()
                ->persistent()
                ->send();
        }

        $data['shared_fee_per_order'] = $count > 0 ? round($totalAllocated / $count, 2) : 0;
        unset($data['order_allocations'], $data['pending_pooled_orders_list'], $data['financial_summary'], $data['assigned_pooled_orders_summary']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $allocations = $this->orderAllocations;

        foreach ($allocations as $alloc) {
            $orderId = $alloc['order_id'] ?? null;
            $fee = (float) ($alloc['assigned_fee'] ?? 0.00);

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $newTotal = max(0.0, round($order->subtotal - $order->discount_amount + $fee, 2));
                    $order->update([
                        'delivery_pool_id' => $this->record->id,
                        'delivery_fee'     => $fee,
                        'total'            => $newTotal,
                        'pooling_status'   => $this->record->status === DeliveryPool::STATUS_SETTLED ? Order::POOLING_SETTLED : Order::POOLING_POOLED,
                    ]);
                }
            }
        }
    }
}
