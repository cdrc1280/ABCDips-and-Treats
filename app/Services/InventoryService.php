<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    public function addStockMovement(
        Ingredient $ingredient,
        string $type,
        float $qty,
        float $unitCost = 0.0,
        ?string $notes = null,
        ?User $user = null
    ): StockMovement {
        $movement = StockMovement::create([
            'ingredient_id'      => $ingredient->id,
            'type'               => $type,
            'qty'                => $qty,
            'unit_cost'          => $unitCost > 0 ? $unitCost : $ingredient->cost_per_unit,
            'notes'              => $notes,
            'created_by_user_id' => $user?->id,
        ]);

        // Update ingredient current stock quantity & unit cost
        $newStock = (float) $ingredient->stock_qty + $qty;
        $ingredient->update([
            'stock_qty'     => max(0.0, $newStock),
            'cost_per_unit' => $unitCost > 0 ? $unitCost : $ingredient->cost_per_unit,
        ]);

        return $movement;
    }

    public function getLowStockIngredients(): Collection
    {
        return Ingredient::whereColumn('stock_qty', '<=', 'min_stock_qty')->get();
    }
}
