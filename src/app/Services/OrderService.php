<?php

namespace App\Services;

use App\Domains\Catalog\Models\Product;
use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Models\Cart;
use App\Domains\Sales\Models\Coupon;
use App\Domains\Sales\Models\Order;
use App\Domains\Sales\Models\OrderItem;
use App\Domains\Sales\Notifications\NewOrderPlaced;
use App\Domains\User\Models\User;
use App\Services\Payments\PaymentManager;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OrderService
{
    /** Hosted payment page for the order just created (GCash / Maya / QR Ph), if any. */
    public ?string $lastCheckoutUrl = null;

    public function __construct(
        private readonly PaymentManager $paymentManager,
        private readonly InventoryDeductionService $inventoryDeductionService
    ) {}

    public function createOrderFromCart(Cart $cart, array $data, ?User $user = null): Order
    {
        $this->lastCheckoutUrl = null;

        // Re-price every line from current product data and re-check the coupon before charging.
        app(CartService::class)->recalculateCartTotals($cart);
        $cart->load(['items.product', 'items' => fn ($q) => $q->whereNull('deleted_at')]);

        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('Cannot create an order from an empty cart.');
        }

        $trackingToken = Str::random(40);
        $fulfillment = $data['fulfillment_type'] ?? 'delivery';
        $deliveryMode = ($fulfillment === 'delivery') ? ($data['delivery_mode'] ?? Order::MODE_PRIORITY) : Order::MODE_PRIORITY;

        $standardFee = (float) ($data['shipping_fee'] ?? 0.00);
        if ($fulfillment === 'pickup') {
            $deliveryFee = 0.00;
            $estimatedSharedFee = 0.00;
            $poolingStatus = Order::POOLING_NOT_POOLED;
        } elseif ($deliveryMode === Order::MODE_POOLING) {
            // Pooled delivery: fee is decided by Admin based on customer location & border distance upon batching
            $estimatedSharedFee = 0.00;
            $deliveryFee = 0.00;
            $poolingStatus = Order::POOLING_AWAITING_ASSIGNMENT;
        } else {
            // Priority delivery: exact live Lalamove API quote based on store location and customer location
            $deliveryFee = $standardFee;
            $estimatedSharedFee = 0.00;
            $poolingStatus = Order::POOLING_NOT_POOLED;
        }

        $subtotal = $cart->subtotal;
        $discount = (float) $cart->discount_amount;
        $total = max(0.0, round($subtotal - $discount + $deliveryFee, 2));

        // The order number is random; on the rare collision with an existing one, try a fresh number.
        for ($attempt = 1; ; $attempt++) {
            $orderNumber = 'ABCD-'.date('Ymd').'-'.strtoupper(Str::random(4));
            try {
                $order = $this->placeOrder($cart, $data, $user, $fulfillment, $deliveryMode, $poolingStatus, $deliveryFee, $estimatedSharedFee, $subtotal, $discount, $total, $orderNumber, $trackingToken);
                break;
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt >= 3 || ! str_contains($e->getMessage(), 'order_number')) {
                    throw $e;
                }
            }
        }

        // Start the payment only after the order is committed: a slow or failing gateway must not hold
        // product locks or roll back a valid order. If it fails, the customer can pay from the order page.
        if ($deliveryMode !== Order::MODE_POOLING) {
            $this->startPayment($order, $data);
        }

        return $order;
    }

    /**
     * Payment kick-off: e-wallets get a hosted checkout URL (lastCheckoutUrl); manual methods stay pending.
     *
     * @param  array<string, mixed>  $data
     */
    private function startPayment(Order $order, array $data): void
    {
        try {
            $payResult = $this->paymentManager->driver((string) $data['payment_method'])->charge($order, $data);
        } catch (\Throwable $e) {
            report($e);

            return;
        }

        if ($payResult['success']) {
            $this->lastCheckoutUrl = $payResult['checkout_url'] ?? null;
            $order->update([
                'payment_reference' => $payResult['reference'],
                'payment_status' => match (true) {
                    $payResult['status'] === 'paid' => 'paid',
                    $this->lastCheckoutUrl !== null => 'awaiting_payment',
                    default => 'pending',
                },
                'paid_at' => $payResult['status'] === 'paid' ? now() : null,
            ]);
        }
    }

    /**
     * Everything that must succeed or fail together: stock, order, lines, coupon, history, cart.
     *
     * @param  array<string, mixed>  $data
     */
    private function placeOrder(Cart $cart, array $data, ?User $user, string $fulfillment, string $deliveryMode, string $poolingStatus, float $deliveryFee, float $estimatedSharedFee, float $subtotal, float $discount, float $total, string $orderNumber, string $trackingToken): Order
    {
        return DB::transaction(function () use ($cart, $data, $user, $fulfillment, $deliveryMode, $poolingStatus, $deliveryFee, $estimatedSharedFee, $subtotal, $discount, $total, $orderNumber, $trackingToken) {
            // Every line must still be for sale (it may have been switched off or deleted after it was
            // added to the basket), and non-custom lines must be in stock. Rows are locked until commit.
            foreach ($cart->items as $item) {
                $lockedProduct = Product::query()->forCustomer()->whereKey($item->product_id)->lockForUpdate()->first();
                if (! $lockedProduct) {
                    $name = $item->product->name ?? __('An item');
                    throw new \RuntimeException(__(':name is no longer available. Please remove it from your basket.', ['name' => $name]));
                }
                if (empty($item->options['is_custom'])) {
                    if ($lockedProduct->stock_qty < $item->qty) {
                        throw new \RuntimeException("Insufficient stock for product: {$lockedProduct->name}. Available stock: {$lockedProduct->stock_qty}.");
                    }
                }
            }

            // Create Order with default PENDING status
            $order = Order::create([
                'order_number' => $orderNumber,
                'tracking_token' => $trackingToken,
                'user_id' => $user->id ?? $cart->user_id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'fulfillment_type' => $fulfillment,
                'delivery_mode' => $deliveryMode,
                'pooling_status' => $poolingStatus,
                'delivery_address' => $data['delivery_address'] ?? null,
                'region' => $data['region'] ?? null,
                'province' => $data['province'] ?? null,
                'city' => $data['city'] ?? null,
                'barangay' => $data['barangay'] ?? null,
                'street_address' => $data['street_address'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'scheduled_time' => $data['scheduled_time'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'coupon_code' => $cart->coupon_code,
                'delivery_fee' => $deliveryFee,
                'estimated_shared_fee' => $estimatedSharedFee,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'payment_status' => 'pending',
                'status' => Order::STATUS_PENDING,
            ]);

            // Copy Cart Items -> Order Items
            foreach ($cart->items as $item) {
                $productName = (! empty($item->options['is_custom']) && ! empty($item->options['custom_title']))
                    ? $item->options['custom_title']
                    : $item->product->name;

                $productSku = (! empty($item->options['is_custom']))
                    ? 'SKU-CUSTOM-CAKE'
                    : $item->product->sku;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $productName,
                    'product_sku' => $productSku,
                    'qty' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'options' => $item->options,
                ]);

                // Deduct product stock quantity and raw ingredient stocks in real-time
                if ($item->product && empty($item->options['is_custom'])) {
                    $item->product->decrement('stock_qty', $item->qty);
                }
            }

            // Real-time raw ingredients & packaging stock deduction
            $this->inventoryDeductionService->deductForOrder($order);

            // Coupon: re-check under a row lock so two checkouts can't both take the last use.
            if ($cart->coupon_code) {
                $coupon = Coupon::query()->where('code', $cart->coupon_code)->lockForUpdate()->first();
                if (! $coupon || ! $coupon->isValid((float) $subtotal)) {
                    throw new \RuntimeException(__('The coupon :code is no longer available. Remove it from your basket and try again.', ['code' => $cart->coupon_code]));
                }
                $coupon->increment('used_count');
            }

            // Log initial pipeline status
            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => Order::STATUS_PENDING,
                'comment' => 'Order placed via online checkout (Pending approval/baking).',
                'changed_by_user_id' => $user?->id,
            ]);

            // Audit Log entry
            AuditLogger::log('created', "Order #{$order->order_number} placed by {$order->customer_name} (\u{20B1}".number_format((float) $order->total, 2).')', $order);

            // Back-office bell: everyone who can see orders (super admins included).
            try {
                $staff = User::query()
                    ->whereHas('roles', fn ($q) => $q->where('name', 'super_admin')
                        ->orWhereHas('permissions', fn ($p) => $p->where('name', OrderPermission::OrdersView->value)))
                    ->orWhereHas('permissions', fn ($q) => $q->where('name', OrderPermission::OrdersView->value))
                    ->get();

                Notification::send($staff, new NewOrderPlaced($order->loadMissing('items')));
            } catch (\Throwable $e) {
                report($e);
            }

            // Clear cart items
            $cart->items()->delete();
            $cart->update(['coupon_code' => null, 'discount_amount' => 0]);

            return $order->load(['items', 'statusHistories']);
        });
    }

    public function updateOrderStatus(Order $order, string $newStatus, ?string $comment = null, ?User $user = null): Order
    {
        $order->transitionTo($newStatus, $comment, $user?->id);

        return $order->fresh(['items', 'statusHistories']);
    }

    public function cancelCustomerOrder(Order $order, User $user): Order
    {
        if ($order->user_id !== $user->id) {
            throw new \InvalidArgumentException('You are not authorized to cancel this order.');
        }

        // Locked so a double click (or a staff status change at the same moment) cannot
        // restock twice or cancel an order that has just been confirmed.
        return DB::transaction(function () use ($order, $user) {
            $order = Order::query()->lockForUpdate()->with('items')->findOrFail($order->id);

            if ($order->status !== Order::STATUS_PENDING) {
                throw new \DomainException('Only orders with Pending status can be cancelled.');
            }

            $order->transitionTo(Order::STATUS_CANCELLED, 'Cancelled by customer.', $user->id);

            foreach ($order->items as $item) {
                if ($item->product_id && empty($item->options['is_custom'])) {
                    Product::where('id', $item->product_id)->increment('stock_qty', $item->qty);
                }
            }

            $this->inventoryDeductionService->revertForOrder($order);

            return $order->fresh(['items', 'statusHistories']);
        });
    }
}
