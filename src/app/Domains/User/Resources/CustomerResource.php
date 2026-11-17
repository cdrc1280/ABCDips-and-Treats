<?php

namespace App\Domains\User\Resources;

use App\Domains\User\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class CustomerResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $streetAddress,
        public ?string $barangay,
        public ?string $city,
        public ?string $province,
        public ?string $region,
        public int $ordersCount,
        public float $totalSpent,
        public bool $emailVerified,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(User $customer, array $context = []): self
    {
        return new self(
            id: $customer->id,
            name: $customer->name,
            email: $customer->email,
            phone: $customer->phone,
            streetAddress: $customer->street_address,
            barangay: $customer->barangay,
            city: $customer->city,
            province: $customer->province,
            region: $customer->region,
            ordersCount: (int) ($customer->orders_count ?? $customer->orders()->count()),
            totalSpent: (float) $customer->orders()->where('payment_status', 'paid')->sum('total'),
            emailVerified: $customer->hasVerifiedEmail(),
            createdAt: $customer->created_at?->toIso8601String(),
            updatedAt: $customer->updated_at?->toIso8601String(),
        );
    }
}
