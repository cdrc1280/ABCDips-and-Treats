<?php

namespace App\Domains\Sales\Resources;

use App\Domains\Sales\Models\DeliveryPool;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class DeliveryPoolResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $poolCode,
        public ?string $zoneName,
        public ?string $region,
        public ?string $province,
        public ?string $city,
        public ?string $barangay,
        public float $totalDeliveryFee,
        public string $status,
        public ?string $statusLabel,
        public array $allocations,
        public ?string $notes,
        public int $ordersCount,
        public float $allocatedFees,
        public float $sharedFeePerOrder,
        public ?string $settledAt,
        public bool $isLocked,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(DeliveryPool $deliveryPool, array $context = []): self
    {
        return new self(
            id: $deliveryPool->id,
            poolCode: $deliveryPool->pool_code,
            zoneName: $deliveryPool->zone_name,
            region: $deliveryPool->region,
            province: $deliveryPool->province,
            city: $deliveryPool->city,
            barangay: $deliveryPool->barangay,
            totalDeliveryFee: (float) $deliveryPool->total_delivery_fee,
            status: (string) $deliveryPool->status,
            statusLabel: self::option(['open' => 'Open', 'building' => 'Building'], $deliveryPool->status),
            allocations: $deliveryPool->orders->map(fn ($o) => ['order_id' => $o->id, 'assigned_fee' => (float) $o->delivery_fee])->values()->all(),
            notes: $deliveryPool->notes,
            ordersCount: (int) ($deliveryPool->orders_count ?? $deliveryPool->orders()->count()),
            allocatedFees: (float) $deliveryPool->orders->sum('delivery_fee'),
            sharedFeePerOrder: (float) $deliveryPool->shared_fee_per_order,
            settledAt: $deliveryPool->settled_at ? Carbon::parse((string) $deliveryPool->settled_at)->toIso8601String() : null,
            isLocked: in_array($deliveryPool->status, ['settled', 'dispatched'], true),
            createdAt: $deliveryPool->created_at?->toIso8601String(),
            updatedAt: $deliveryPool->updated_at?->toIso8601String(),
        );
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
