<?php

namespace App\Domains\Inventory\Resources;

use App\Domains\Inventory\Models\Supplier;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class SupplierResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $contactPerson,
        public string $email,
        public string $phone,
        public string $paymentTerms,
        public string $address,
        public ?string $notes,
        public int $ingredientsCount,
        public int $purchaseOrdersCount,
        public bool $inUse,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Supplier $supplier, array $context = []): self
    {
        return new self(
            id: $supplier->id,
            name: $supplier->name,
            contactPerson: $supplier->contact_person,
            email: $supplier->email,
            phone: $supplier->phone,
            paymentTerms: $supplier->payment_terms,
            address: $supplier->address,
            notes: $supplier->notes,
            ingredientsCount: (int) ($supplier->ingredients_count ?? $supplier->ingredients()->count()),
            purchaseOrdersCount: (int) ($supplier->purchase_orders_count ?? $supplier->purchaseOrders()->count()),
            inUse: ($supplier->ingredients_count ?? $supplier->ingredients()->count()) + ($supplier->purchase_orders_count ?? $supplier->purchaseOrders()->count()) > 0,
            createdAt: $supplier->created_at?->toIso8601String(),
            updatedAt: $supplier->updated_at?->toIso8601String(),
        );
    }
}
