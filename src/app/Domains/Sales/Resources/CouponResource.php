<?php

namespace App\Domains\Sales\Resources;

use App\Domains\Sales\Models\Coupon;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class CouponResource extends Resource
{
    public function __construct(
        public string $id,
        public string $code,
        public string $type,
        public ?string $typeLabel,
        public float $value,
        public float $minSpend,
        public ?float $maxDiscount,
        public ?int $usageLimit,
        public ?string $startsAt,
        public ?string $expiresAt,
        public bool $isActive,
        public int $usedCount,
        public string $state,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Coupon $coupon, array $context = []): self
    {
        return new self(
            id: $coupon->id,
            code: $coupon->code,
            type: (string) $coupon->type,
            typeLabel: self::option(['fixed' => 'Fixed amount (₱)', 'percent' => 'Percentage (%)'], $coupon->type),
            value: (float) $coupon->value,
            minSpend: (float) $coupon->min_spend,
            maxDiscount: $coupon->max_discount === null ? null : (float) $coupon->max_discount,
            usageLimit: $coupon->usage_limit === null ? null : (int) $coupon->usage_limit,
            startsAt: self::dateTime($coupon->starts_at),
            expiresAt: self::dateTime($coupon->expires_at),
            isActive: (bool) $coupon->is_active,
            usedCount: (int) $coupon->used_count,
            state: match (true) {
                ! $coupon->is_active => 'inactive', $coupon->expires_at && $coupon->expires_at->isPast() => 'expired', $coupon->starts_at && $coupon->starts_at->isFuture() => 'scheduled', $coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit => 'exhausted', default => 'active'
            },
            createdAt: $coupon->created_at?->toIso8601String(),
            updatedAt: $coupon->updated_at?->toIso8601String(),
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
