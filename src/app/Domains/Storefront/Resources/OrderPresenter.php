<?php

namespace App\Domains\Storefront\Resources;

use App\Domains\Sales\Models\Order;

/** Customer-facing order shape (account, confirmation, tracking). */
final class OrderPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function summary(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_label' => ucwords(str_replace('_', ' ', (string) $order->status)),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'fulfillment_type' => $order->fulfillment_type,
            'total' => (float) $order->total,
            'items_count' => $order->relationLoaded('items') ? (int) $order->items->sum('qty') : null,
            'created_at' => $order->created_at?->toIso8601String(),
            'can_cancel' => $order->status === Order::STATUS_PENDING,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(Order $order): array
    {
        $order->loadMissing(['items.product', 'statusHistories', 'deliveryPool']);
        $invoiceLocked = $order->delivery_mode === Order::MODE_POOLING && $order->pooling_status !== Order::POOLING_SETTLED;

        return [
            ...self::summary($order),
            'tracking_token' => $order->tracking_token,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'delivery_mode' => $order->delivery_mode ?? Order::MODE_PRIORITY,
            'pooling_status' => $order->pooling_status ?? Order::POOLING_NOT_POOLED,
            'delivery_address' => $order->delivery_address,
            'city' => $order->city,
            'scheduled_time' => $order->scheduled_time?->toIso8601String(),
            'notes' => $order->notes,
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'coupon_code' => $order->coupon_code,
            'delivery_fee' => (float) $order->delivery_fee,
            'estimated_shared_fee' => (float) $order->estimated_shared_fee,
            'payment_reference' => $order->payment_reference,
            'invoice_available' => ! $invoiceLocked,
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'product_slug' => $item->product?->slug,
                'image_url' => $item->product?->primary_image_url,
                'qty' => (int) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => (float) $item->subtotal,
                'options' => $item->options ?? null,
            ])->values()->all(),
            'timeline' => $order->statusHistories->sortBy('created_at')->map(fn ($h) => [
                'status' => $h->to_status,
                'label' => ucwords(str_replace('_', ' ', (string) $h->to_status)),
                'comment' => $h->comment,
                'created_at' => $h->created_at?->toIso8601String(),
            ])->values()->all(),
        ];
    }
}
