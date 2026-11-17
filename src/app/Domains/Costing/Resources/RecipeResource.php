<?php

namespace App\Domains\Costing\Resources;

use App\Domains\Costing\Models\Recipe;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class RecipeResource extends Resource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $productId,
        public ?string $productName,
        public int $yieldQty,
        public int $prepTimeMinutes,
        public int $bakingTimeMinutes,
        public ?string $instructions,
        public array $ingredients,
        public array $packaging,
        public float $overheadPct,
        public float $markupPct,
        public float $laborPct,
        public float $ingredientCost,
        public float $packagingCost,
        public float $totalCost,
        public float $unitCost,
        public float $unitSellingPrice,
        public float $grossMargin,
        public ?float $productPrice,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(Recipe $recipe, array $context = []): self
    {
        return new self(
            id: $recipe->id,
            name: $recipe->name,
            productId: $recipe->product_id,
            productName: $recipe->product?->name,
            yieldQty: (int) $recipe->yield_qty,
            prepTimeMinutes: (int) $recipe->prep_time_minutes,
            bakingTimeMinutes: (int) $recipe->baking_time_minutes,
            instructions: $recipe->instructions,
            ingredients: $recipe->recipeIngredients->map(fn ($row) => ['id' => $row->id, 'ingredient_id' => $row->ingredient_id, 'qty_required' => $row->qty_required, 'unit' => $row->unit, 'line_cost' => (float) $row->line_cost, 'ingredient_name' => $row->ingredient?->name])->values()->all(),
            packaging: $recipe->recipePackagings->map(fn ($row) => ['id' => $row->id, 'packaging_material_id' => $row->packaging_material_id, 'name' => $row->name, 'unit' => $row->unit, 'package_qty' => $row->package_qty, 'package_cost' => $row->package_cost, 'qty_used' => $row->qty_used])->values()->all(),
            overheadPct: (float) $recipe->overhead_pct,
            markupPct: (float) $recipe->markup_pct,
            laborPct: (float) $recipe->labor_pct,
            ingredientCost: (float) $recipe->total_ingredient_cost,
            packagingCost: (float) $recipe->total_packaging_cost,
            totalCost: (float) $recipe->total_cost,
            unitCost: (float) $recipe->unit_cost,
            unitSellingPrice: (float) $recipe->unit_selling_price,
            grossMargin: (float) $recipe->gross_margin_percentage,
            productPrice: $recipe->product ? (float) $recipe->product->price : null,
            createdAt: $recipe->created_at?->toIso8601String(),
            updatedAt: $recipe->updated_at?->toIso8601String(),
        );
    }
}
