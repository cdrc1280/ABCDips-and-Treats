<?php

namespace App\Domains\Inventory\Resources;

use App\Domains\Inventory\Models\PackagingMaterial;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class PackagingMaterialResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $sku,
        public string $type,
        public ?string $typeLabel,
        public string $unit,
        public float $costPerUnit,
        public float $stockQty,
        public float $minStockQty,
        public bool $isActive,
        public ?string $notes,
        public bool $isLowStock,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(PackagingMaterial $packagingMaterial, array $context = []): self
    {
        return new self(
            id: $packagingMaterial->id,
            name: $packagingMaterial->name,
            sku: $packagingMaterial->sku,
            type: (string) $packagingMaterial->type,
            typeLabel: self::option(['box' => 'Box', 'bag' => 'Bag', 'board' => 'Board', 'container' => 'Container', 'ribbon' => 'Ribbon', 'label' => 'Label', 'sticker' => 'Sticker', 'tape' => 'Tape', 'wrap' => 'Wrap', 'other' => 'Other'], $packagingMaterial->type),
            unit: $packagingMaterial->unit,
            costPerUnit: (float) $packagingMaterial->cost_per_unit,
            stockQty: (float) $packagingMaterial->stock_qty,
            minStockQty: (float) $packagingMaterial->min_stock_qty,
            isActive: (bool) $packagingMaterial->is_active,
            notes: $packagingMaterial->notes,
            isLowStock: (float) $packagingMaterial->stock_qty <= (float) $packagingMaterial->min_stock_qty,
            createdAt: $packagingMaterial->created_at?->toIso8601String(),
            updatedAt: $packagingMaterial->updated_at?->toIso8601String(),
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
