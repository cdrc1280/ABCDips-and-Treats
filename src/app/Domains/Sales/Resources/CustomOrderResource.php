<?php

namespace App\Domains\Sales\Resources;

use App\Domains\Sales\Models\CustomOrder;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class CustomOrderResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $referenceNumber,
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public ?string $eventDate,
        public int $servingsCount,
        public int $tiersCount,
        public ?string $flavorPreference,
        public ?string $frostingType,
        public string $themeDescription,
        public ?float $budgetRangeMin,
        public ?float $budgetRangeMax,
        public ?float $quotedPrice,
        public string $status,
        public ?string $statusLabel,
        public ?string $staffNotes,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(CustomOrder $customOrder, array $context = []): self
    {
        return new self(
            id: $customOrder->id,
            referenceNumber: $customOrder->reference_number,
            customerName: $customOrder->customer_name,
            customerEmail: $customOrder->customer_email,
            customerPhone: $customOrder->customer_phone,
            eventDate: self::date($customOrder->event_date),
            servingsCount: (int) $customOrder->servings_count,
            tiersCount: (int) $customOrder->tiers_count,
            flavorPreference: $customOrder->flavor_preference,
            frostingType: $customOrder->frosting_type,
            themeDescription: $customOrder->theme_description,
            budgetRangeMin: $customOrder->budget_range_min === null ? null : (float) $customOrder->budget_range_min,
            budgetRangeMax: $customOrder->budget_range_max === null ? null : (float) $customOrder->budget_range_max,
            quotedPrice: $customOrder->quoted_price === null ? null : (float) $customOrder->quoted_price,
            status: (string) $customOrder->status,
            statusLabel: self::option(['inquiry' => 'Inquiry', 'quoted' => 'Quoted', 'deposit_paid' => 'Deposit paid', 'in_production' => 'In production', 'ready' => 'Ready', 'completed' => 'Completed', 'cancelled' => 'Cancelled'], $customOrder->status),
            staffNotes: $customOrder->staff_notes,
            createdAt: $customOrder->created_at?->toIso8601String(),
            updatedAt: $customOrder->updated_at?->toIso8601String(),
        );
    }

    private static function date(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : substr((string) $value, 0, 10);
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
