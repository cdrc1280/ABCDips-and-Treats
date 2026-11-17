<?php

namespace App\Domains\Inventory\Actions;

use App\Domains\Inventory\Models\Ingredient;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteIngredientAction
{
    public function execute(Ingredient $ingredient): void
    {
        if ($ingredient->recipeIngredients()->exists()) {
            throw new DomainRuleViolation(__(':name is used in recipes. Remove it from those recipes first.', ['name' => $ingredient->name]));
        }

        DB::transaction(fn () => $ingredient->delete());
    }
}
