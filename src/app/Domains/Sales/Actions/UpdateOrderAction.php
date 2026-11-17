<?php

namespace App\Domains\Sales\Actions;

use App\Domains\Sales\Data\OrderData;
use App\Domains\Sales\Models\Order;
use Illuminate\Support\Facades\DB;

final class UpdateOrderAction
{
    public function execute(Order $order, OrderData $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'customer_name' => $data->customerName,
                'customer_email' => $data->customerEmail,
                'customer_phone' => $data->customerPhone,
                'delivery_address' => $data->deliveryAddress,
                'city' => $data->city,
                'postal_code' => $data->postalCode,
                'scheduled_time' => $data->scheduledTime,
                'notes' => $data->notes,
                'payment_status' => $data->paymentStatus,
                'payment_reference' => $data->paymentReference,
            ]);

            return $order;
        });
    }
}
