<?php

namespace App\Filament\Resources\DeliveryPoolResource\Pages;

use App\Filament\Resources\DeliveryPoolResource;
use App\Models\DeliveryPool;
use App\Models\Order;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryPool extends EditRecord
{
    protected static string $resource = DeliveryPoolResource::class;

    protected array $orderAllocations = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['order_allocations'] = $this->record->orders()->get()->map(fn(Order $o) => [
            'order_id'     => $o->id,
            'assigned_fee' => (float) $o->delivery_fee,
        ])->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->orderAllocations = $data['order_allocations'] ?? [];
        $count = max(1, count($this->orderAllocations));
        $totalAllocated = array_sum(array_column($this->orderAllocations, 'assigned_fee'));
        $routeFee = (float) ($data['total_delivery_fee'] ?? 0);

        if ($totalAllocated < $routeFee && count($this->orderAllocations) > 0) {
            $remaining = number_format($routeFee - $totalAllocated, 2);
            \Filament\Notifications\Notification::make()
                ->title('Shipping Fee Shortfall')
                ->body("The total allocated fees (\u{20B1}" . number_format($totalAllocated, 2) . ") do not fully cover the route fee (\u{20B1}" . number_format($routeFee, 2) . "). Remaining: \u{20B1}{$remaining}.")
                ->warning()
                ->persistent()
                ->send();
        }

        $data['shared_fee_per_order'] = $count > 0 ? round($totalAllocated / $count, 2) : 0;
        unset($data['order_allocations'], $data['pending_pooled_orders_list'], $data['financial_summary'], $data['assigned_pooled_orders_summary']);

        return $data;
    }

    protected function afterSave(): void
    {
        $allocations = $this->orderAllocations;
        $assignedIds = array_filter(array_column($allocations, 'order_id'));

        // Detach unselected orders from this pool
        Order::where('delivery_pool_id', $this->record->id)
            ->whereNotIn('id', $assignedIds)
            ->update([
                'delivery_pool_id' => null,
                'pooling_status'   => Order::POOLING_AWAITING_ASSIGNMENT,
                'delivery_fee'     => 0.00,
            ]);

        // Attach & update allocated orders
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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
