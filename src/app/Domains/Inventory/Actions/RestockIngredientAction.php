<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Models\Ingredient;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;

/** Add stock. */
final class RestockIngredientAction
{
    public function execute(Ingredient $ingredient, mixed $value): Ingredient
    {
        DB::transaction(function () use ($ingredient, $value) {
            app(InventoryService::class)->addStockMovement($ingredient, 'purchase', (float) $value, 0.0, 'Restocked from the back office', auth()->user());
        });

        return $ingredient->refresh();
    }
}
