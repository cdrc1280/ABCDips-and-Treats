<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Payments\PaymentManager;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private readonly PaymentManager $paymentManager
    ) {}

    public function createOrderFromCart(Cart $cart, array $data, ?User $user = null): Order
    {
        $cart->load(['items.product', 'items' => fn ($q) => $q->whereNull('deleted_at')]);

        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('Cannot create an order from an empty cart.');
        }

        $orderNumber   = 'ABCD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $trackingToken = Str::random(40);
        $fulfillment   = $data['fulfillment_type'] ?? 'delivery';
        $deliveryFee   = $fulfillment === 'delivery' ? 120.00 : 0.00;

        $subtotal = $cart->subtotal;
        $discount = (float) $cart->discount_amount;
        $total    = max(0.0, round($subtotal - $discount + $deliveryFee, 2));

        // Create Order
        $order = Order::create([
            'order_number'      => $orderNumber,
            'tracking_token'    => $trackingToken,
            'user_id'           => $user?->id ?? $cart->user_id,
            'customer_name'     => $data['customer_name'],
            'customer_email'    => $data['customer_email'],
            'customer_phone'    => $data['customer_phone'],
            'fulfillment_type'  => $fulfillment,
            'delivery_address'  => $data['delivery_address'] ?? null,
            'city'              => $data['city'] ?? null,
            'postal_code'       => $data['postal_code'] ?? null,
            'scheduled_time'    => $data['scheduled_time'] ?? null,
            'notes'             => $data['notes'] ?? null,
            'subtotal'          => $subtotal,
            'discount_amount'   => $discount,
            'coupon_code'       => $cart->coupon_code,
            'delivery_fee'      => $deliveryFee,
            'total'             => $total,
            'payment_method'    => $data['payment_method'],
            'payment_status'    => 'pending',
            'status'            => Order::STATUS_PENDING,
        ]);

        // Copy Cart Items -> Order Items
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,
                'product_sku'  => $item->product->sku,
                'qty'          => $item->qty,
                'unit_price'   => $item->unit_price,
                'subtotal'     => $item->subtotal,
                'options'      => $item->options,
            ]);

            // Deduct stock quantity
            if ($item->product) {
                $item->product->decrement('stock_qty', $item->qty);
            }
        }

        // Process Payment Gateway Charge
        $gateway = $this->paymentManager->driver($data['payment_method']);
        $payResult = $gateway->charge($order, $data);

        if ($payResult['success']) {
            $order->update([
                'payment_reference' => $payResult['reference'],
                'payment_status'    => $payResult['status'] === 'paid' ? 'paid' : 'pending',
                'paid_at'           => $payResult['status'] === 'paid' ? now() : null,
                'status'            => Order::STATUS_CONFIRMED,
            ]);
        }

        // Increment coupon use count
        if ($cart->coupon_code) {
            Coupon::where('code', $cart->coupon_code)->increment('used_count');
        }

        // Log initial pipeline status
        $order->statusHistories()->create([
            'from_status'        => null,
            'to_status'          => $order->status,
            'comment'            => 'Order created via checkout.',
            'changed_by_user_id' => $user?->id,
        ]);

        // Clear cart items
        $cart->items()->delete();
        $cart->update(['coupon_code' => null, 'discount_amount' => 0]);

        return $order->load(['items', 'statusHistories']);
    }

    public function updateOrderStatus(Order $order, string $newStatus, ?string $comment = null, ?User $user = null): Order
    {
        $order->transitionTo($newStatus, $comment, $user?->id);
        return $order->fresh(['items', 'statusHistories']);
    }

    public function getOrderByTrackingToken(string $token): ?Order
    {
        return Order::where('tracking_token', $token)
            ->with(['items.product', 'statusHistories.changedBy'])
            ->first();
    }
}
