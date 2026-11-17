<?php

namespace App\Domains\Sales\Notifications;

use App\Domains\Sales\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/** Back-office bell entry for a new storefront order. */
class NewOrderPlaced extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $order = $this->order;
        $pooling = $order->delivery_mode === Order::MODE_POOLING;
        $items = $order->items->take(2)->map(fn ($i) => "{$i->qty}× {$i->product_name}")->implode(', ');
        if ($order->items->count() > 2) {
            $items .= ' + more';
        }

        $amount = '₱'.number_format((float) ($pooling ? $order->subtotal : $order->total), 2);
        $where = $pooling
            ? implode(', ', array_filter([$order->barangay, $order->city, $order->province])) ?: ucfirst((string) $order->fulfillment_type)
            : ucfirst((string) $order->fulfillment_type);

        return [
            'kind' => $pooling ? 'order.pooling' : 'order.new',
            'title' => ($pooling ? 'New group pooling order' : 'New order')." #{$order->order_number}",
            'body' => "{$order->customer_name} · {$amount} · {$where}".($items !== '' ? " — {$items}" : ''),
            'url' => route('admin.orders.edit', $order, absolute: false),
            'order_id' => $order->id,
        ];
    }
}
