<?php

namespace App\Filament\Resources\DeliveryPoolResource\Pages;

use App\Filament\Resources\DeliveryPoolResource;
use App\Models\Order;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryPool extends EditRecord
{
    protected static string $resource = DeliveryPoolResource::class;

    protected array $assignedOrders = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['assigned_orders'] = $this->record->orders()->pluck('id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->assignedOrders = $data['assigned_orders'] ?? [];
        $count    = max(1, count($this->assignedOrders));
        $totalFee = (float) ($data['total_delivery_fee'] ?? 150.00);

        $data['shared_fee_per_order'] = round($totalFee / $count, 2);
        unset($data['assigned_orders']);

        return $data;
    }

    protected function afterSave(): void
    {
        $assigned = $this->assignedOrders;
        $shared   = $this->record->shared_fee_per_order;

        // Detach unselected orders from this pool
        Order::where('delivery_pool_id', $this->record->id)
            ->whereNotIn('id', $assigned)
            ->update([
                'delivery_pool_id' => null,
                'pooling_status'   => Order::POOLING_AWAITING_ASSIGNMENT,
            ]);

        // Attach selected orders to this pool
        if (!empty($assigned)) {
            Order::whereIn('id', $assigned)->update([
                'delivery_pool_id' => $this->record->id,
                'pooling_status'   => $this->record->status === 'settled' ? Order::POOLING_SETTLED : Order::POOLING_POOLED,
                'delivery_fee'     => $shared,
            ]);

            foreach (Order::whereIn('id', $assigned)->get() as $order) {
                $newTotal = max(0.0, round($order->subtotal - $order->discount_amount + $shared, 2));
                $order->update(['total' => $newTotal]);
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
