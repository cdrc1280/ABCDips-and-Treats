<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Data\DeliveryPoolData;
use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateDeliveryPoolAction
{
    public function execute(DeliveryPoolData $data): DeliveryPool
    {
        return DB::transaction(function () use ($data) {
            $deliveryPool = DeliveryPool::query()->create([
                'pool_code' => $data->poolCode ?? 'POOL-'.now()->format('Ymd').'-'.strtoupper(Str::random(4)),
                'zone_name' => $data->zoneName,
                'region' => $data->region,
                'province' => $data->province,
                'city' => $data->city,
                'barangay' => $data->barangay,
                'total_delivery_fee' => $data->totalDeliveryFee,
                'status' => $data->status,
                'notes' => $data->notes,
            ]);
            $keep = collect($data->allocations)->pluck('order_id')->map(fn ($id) => (string) $id)->filter()->unique();
            Order::query()->where('delivery_pool_id', $deliveryPool->id)->whereNotIn('id', $keep)
                ->update(['delivery_pool_id' => null, 'pooling_status' => Order::POOLING_AWAITING_ASSIGNMENT, 'delivery_fee' => 0]);
            foreach ($data->allocations as $row) {
                Order::query()->whereKey((string) $row['order_id'])->where('delivery_mode', Order::MODE_POOLING)
                    ->update(['delivery_pool_id' => $deliveryPool->id, 'pooling_status' => Order::POOLING_POOLED, 'delivery_fee' => round((float) ($row['assigned_fee'] ?? 0), 2)]);
            }

            return $deliveryPool;
        });
    }
}
