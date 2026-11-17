<?php

namespace App\Domains\Sales\Resources;

use App\Domains\Sales\Models\Order;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class OrderResource extends Resource
{
    public function __construct(
        public string $id,
        public string $orderNumber,
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public ?string $deliveryAddress,
        public ?string $city,
        public ?string $postalCode,
        public ?string $scheduledTime,
        public ?string $notes,
        public string $paymentStatus,
        public ?string $paymentStatusLabel,
        public ?string $paymentReference,
        public string $status,
        public ?string $statusLabel,
        public string $fulfillmentType,
        public string $deliveryMode,
        public string $poolingStatus,
        public string $paymentMethod,
        public float $subtotal,
        public float $discountAmount,
        public ?string $couponCode,
        public float $deliveryFee,
        public float $total,
        public ?string $paidAt,
        public int $itemsCount,
        public string $trackingToken,
        public array $items,
        public array $history,
        public ?string $createdAt,
        public ?string $updatedAt,
        /** @var list<string> statuses the order may move to next */
        public array $nextStatuses = [],
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Order $order, array $context = []): self
    {
        return new self(
            id: $order->id,
            orderNumber: $order->order_number,
            customerName: $order->customer_name,
            customerEmail: $order->customer_email,
            customerPhone: $order->customer_phone,
            deliveryAddress: $order->delivery_address,
            city: $order->city,
            postalCode: $order->postal_code,
            scheduledTime: self::dateTime($order->scheduled_time),
            notes: $order->notes,
            paymentStatus: (string) $order->payment_status,
            paymentStatusLabel: self::option(['pending' => 'Pending', 'awaiting_payment' => 'Awaiting payment', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'], $order->payment_status),
            paymentReference: $order->payment_reference,
            status: $order->status,
            statusLabel: self::option(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'preparing' => 'Preparing', 'packaging' => 'Packaging', 'out_for_delivery' => 'Out for delivery', 'ready_for_pickup' => 'Ready for pickup', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded', 'archived' => 'Archived'], $order->status),
            fulfillmentType: $order->fulfillment_type,
            deliveryMode: $order->delivery_mode,
            poolingStatus: $order->pooling_status,
            paymentMethod: $order->payment_method,
            subtotal: (float) $order->subtotal,
            discountAmount: (float) $order->discount_amount,
            couponCode: $order->coupon_code,
            deliveryFee: (float) $order->delivery_fee,
            total: (float) $order->total,
            paidAt: $order->paid_at ? Carbon::parse((string) $order->paid_at)->toIso8601String() : null,
            itemsCount: (int) ($order->items_count ?? $order->items()->count()),
            trackingToken: $order->tracking_token,
            items: $order->relationLoaded('items') ? $order->items->map(fn ($i) => ['id' => $i->id, 'product_name' => $i->product_name, 'product_sku' => $i->product_sku, 'qty' => (int) $i->qty, 'unit_price' => (float) $i->unit_price, 'subtotal' => (float) $i->subtotal, 'options' => $i->options])->values()->all() : [],
            history: $order->relationLoaded('statusHistories') ? $order->statusHistories->sortByDesc('id')->map(fn ($h) => ['id' => $h->id, 'from_status' => $h->from_status, 'to_status' => $h->to_status, 'comment' => $h->comment, 'changed_by' => $h->changedBy?->name, 'created_at' => $h->created_at?->toIso8601String()])->values()->all() : [],
            createdAt: $order->created_at?->toIso8601String(),
            updatedAt: $order->updated_at?->toIso8601String(),
            nextStatuses: $order->allowedTransitions(),
        );
    }

    private static function dateTime(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return ($value instanceof \DateTimeInterface ? Carbon::instance($value) : Carbon::parse((string) $value))->toIso8601String();
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
