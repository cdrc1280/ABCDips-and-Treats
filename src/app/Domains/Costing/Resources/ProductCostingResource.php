<?php

namespace App\Domains\Costing\Resources;

use App\Domains\Costing\Models\ProductCosting;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class ProductCostingResource extends Resource
{
    public function __construct(
        public string $id,
        public ?string $name,
        public ?string $productId,
        public ?string $productName,
        public float $yieldQty,
        public string $yieldUnit,
        public array $items,
        public float $overheadPct,
        public float $markupPct,
        public float $laborPct,
        public float $rawCost,
        public float $totalCost,
        public float $sellingPrice,
        public float $pricePerPiece,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(ProductCosting $productCosting, array $context = []): self
    {
        return new self(
            id: $productCosting->id,
            name: $productCosting->name,
            productId: $productCosting->product_id === null ? null : $productCosting->product_id,
            productName: $productCosting->product?->name,
            yieldQty: (float) $productCosting->yield_qty,
            yieldUnit: $productCosting->yield_unit,
            items: $productCosting->costingItems->map(fn ($row) => ['id' => $row->id, 'group' => $row->group, 'ingredient_id' => $row->ingredient_id, 'packaging_material_id' => $row->packaging_material_id, 'name' => $row->name, 'unit' => $row->unit, 'package_amount' => $row->package_amount, 'package_price' => $row->package_price, 'qty_used' => $row->qty_used, 'line_cost' => (float) $row->line_cost])->values()->all(),
            overheadPct: (float) $productCosting->overhead_pct,
            markupPct: (float) $productCosting->markup_pct,
            laborPct: (float) $productCosting->labor_pct,
            rawCost: (float) $productCosting->raw_cost,
            totalCost: (float) $productCosting->total_cost,
            sellingPrice: (float) $productCosting->selling_price,
            pricePerPiece: (float) $productCosting->price_per_piece,
            createdAt: $productCosting->created_at?->toIso8601String(),
            updatedAt: $productCosting->updated_at?->toIso8601String(),
        );
    }
}
