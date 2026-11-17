<?php

namespace App\Domains\Costing\Actions;

use App\Domains\Costing\Models\Recipe;
use Illuminate\Support\Facades\DB;

/** Use suggested price. */
final class ApplyPriceRecipeAction
{
    public function execute(Recipe $recipe): Recipe
    {
        DB::transaction(function () use ($recipe) {
            $recipe->product?->update(['price' => round((float) $recipe->unit_selling_price, 2)]);
        });

        return $recipe->refresh();
    }
}
