<?php

namespace App\Domains\Costing\Actions;

use App\Domains\Costing\Data\RecipeData;
use App\Domains\Costing\Models\Recipe;
use Illuminate\Support\Facades\DB;

final class UpdateRecipeAction
{
    public function execute(Recipe $recipe, RecipeData $data): Recipe
    {
        return DB::transaction(function () use ($recipe, $data) {
            $recipe->update([
                'name' => $data->name,
                'product_id' => $data->productId,
                'yield_qty' => $data->yieldQty,
                'prep_time_minutes' => $data->prepTimeMinutes,
                'baking_time_minutes' => $data->bakingTimeMinutes,
                'instructions' => $data->instructions,
                'overhead_pct' => $data->overheadPct,
                'markup_pct' => $data->markupPct,
                'labor_pct' => $data->laborPct,
            ]);
            $recipe->recipeIngredients()->delete();
            foreach ($data->ingredients as $row) {
                $recipe->recipeIngredients()->create(['ingredient_id' => isset($row['ingredient_id']) && $row['ingredient_id'] !== '' ? (string) $row['ingredient_id'] : null, 'qty_required' => isset($row['qty_required']) && $row['qty_required'] !== '' ? (float) $row['qty_required'] : null, 'unit' => isset($row['unit']) && $row['unit'] !== '' ? (string) $row['unit'] : null]);
            }
            $recipe->recipePackagings()->delete();
            foreach ($data->packaging as $row) {
                $recipe->recipePackagings()->create(['packaging_material_id' => isset($row['packaging_material_id']) && $row['packaging_material_id'] !== '' ? (string) $row['packaging_material_id'] : null, 'name' => isset($row['name']) && $row['name'] !== '' ? (string) $row['name'] : null, 'unit' => isset($row['unit']) && $row['unit'] !== '' ? (string) $row['unit'] : null, 'package_qty' => isset($row['package_qty']) && $row['package_qty'] !== '' ? (float) $row['package_qty'] : null, 'package_cost' => isset($row['package_cost']) && $row['package_cost'] !== '' ? (float) $row['package_cost'] : null, 'qty_used' => isset($row['qty_used']) && $row['qty_used'] !== '' ? (float) $row['qty_used'] : null]);
            }

            return $recipe;
        });
    }
}
