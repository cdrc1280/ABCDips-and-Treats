<?php

namespace App\Domains\Inventory\Resources;

use App\Domains\Inventory\Models\Ingredient;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class IngredientResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $sku,
        public ?string $supplierId,
        public ?string $supplierName,
        public string $unit,
        public ?string $unitLabel,
        public float $itemUnit,
        public float $itemPrice,
        public float $stockQty,
        public float $minStockQty,
        public float $reorderQty,
        public float $costPerUnit,
        public bool $isLowStock,
        public int $usedInRecipes,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Ingredient $ingredient, array $context = []): self
    {
        return new self(
            id: $ingredient->id,
            name: $ingredient->name,
            sku: $ingredient->sku,
            supplierId: $ingredient->supplier_id === null ? null : $ingredient->supplier_id,
            supplierName: $ingredient->supplier?->name,
            unit: (string) $ingredient->unit,
            unitLabel: self::option(['grams' => 'Grams (g)', 'kg' => 'Kilograms (kg)', 'ml' => 'Milliliters (ml)', 'L' => 'Liters (L)', 'piece' => 'Piece (pcs)', 'box' => 'Box / pack'], $ingredient->unit),
            itemUnit: (float) $ingredient->item_unit,
            itemPrice: (float) $ingredient->item_price,
            stockQty: (float) $ingredient->stock_qty,
            minStockQty: (float) $ingredient->min_stock_qty,
            reorderQty: (float) $ingredient->reorder_qty,
            costPerUnit: (float) $ingredient->cost_per_unit,
            isLowStock: (float) $ingredient->stock_qty <= (float) $ingredient->min_stock_qty,
            usedInRecipes: (int) ($ingredient->recipe_ingredients_count ?? $ingredient->recipeIngredients()->count()),
            createdAt: $ingredient->created_at?->toIso8601String(),
            updatedAt: $ingredient->updated_at?->toIso8601String(),
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
