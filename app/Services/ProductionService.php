<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\ProductionBatch;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Str;

class ProductionService
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function createBatch(Recipe $recipe, int $plannedQty = 1, ?User $baker = null, ?string $notes = null): ProductionBatch
    {
        $batchNumber = 'BATCH-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        return ProductionBatch::create([
            'batch_number'  => $batchNumber,
            'recipe_id'     => $recipe->id,
            'product_id'    => $recipe->product_id,
            'planned_qty'   => $plannedQty,
            'status'        => ProductionBatch::STATUS_PLANNED,
            'baker_user_id' => $baker?->id,
            'notes'         => $notes,
        ]);
    }

    public function completeBatch(ProductionBatch $batch, ?int $actualYield = null, ?User $user = null): ProductionBatch
    {
        if ($batch->status === ProductionBatch::STATUS_COMPLETED) {
            return $batch;
        }

        $recipe = $batch->recipe->load(['recipeIngredients.ingredient', 'product']);
        $yield = $actualYield ?? ($batch->planned_qty * $recipe->yield_qty);

        // 1. Deduct raw ingredients BOM stock
        foreach ($recipe->recipeIngredients as $ri) {
            $ingredient = $ri->ingredient;
            if ($ingredient) {
                // Total required = (planned_qty * qty_required)
                $qtyNeeded = (float) $ri->qty_required * $batch->planned_qty;
                
                // Convert g -> kg if needed
                $multiplier = match (strtolower($ri->unit)) {
                    'g'     => strtolower($ingredient->unit) === 'kg' ? 0.001 : 1.0,
                    'ml'    => strtolower($ingredient->unit) === 'l'  ? 0.001 : 1.0,
                    default => 1.0,
                };
                $deductQty = $qtyNeeded * $multiplier;

                $this->inventoryService->addStockMovement(
                    $ingredient,
                    'production_usage',
                    -$deductQty,
                    (float) $ingredient->cost_per_unit,
                    "Automated usage for production batch {$batch->batch_number}",
                    $user
                );
            }
        }

        // 2. Increment finished product stock quantity
        if ($recipe->product) {
            $recipe->product->increment('stock_qty', $yield);
        }

        // 3. Mark batch completed
        $batch->update([
            'actual_yield_qty' => $yield,
            'status'           => ProductionBatch::STATUS_COMPLETED,
            'completed_at'     => now(),
        ]);

        return $batch->fresh(['recipe', 'product']);
    }
}
