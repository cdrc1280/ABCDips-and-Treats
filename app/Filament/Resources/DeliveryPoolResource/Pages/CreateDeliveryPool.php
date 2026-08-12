<?php

namespace App\Filament\Resources\DeliveryPoolResource\Pages;

use App\Filament\Resources\DeliveryPoolResource;
use App\Models\Order;
use Filament\Resources\Pages\CreateRecord;

class CreateDeliveryPool extends CreateRecord
{
    protected static string $resource = DeliveryPoolResource::class;

    protected array $assignedOrders = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->assignedOrders = $data['assigned_orders'] ?? [];
        $count    = max(1, count($this->assignedOrders));
        $totalFee = (float) ($data['total_delivery_fee'] ?? 150.00);

        $data['shared_fee_per_order'] = round($totalFee / $count, 2);
        unset($data['assigned_orders']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $assigned = $this->assignedOrders;
        $shared   = $this->record->shared_fee_per_order;

        if (!empty($assigned)) {
            Order::whereIn('id', $assigned)->update([
                'delivery_pool_id' => $this->record->id,
                'pooling_status'   => Order::POOLING_POOLED,
                'delivery_fee'     => $shared,
            ]);

            foreach (Order::whereIn('id', $assigned)->get() as $order) {
                $newTotal = max(0.0, round($order->subtotal - $order->discount_amount + $shared, 2));
                $order->update(['total' => $newTotal]);
            }
        }
    }
}
