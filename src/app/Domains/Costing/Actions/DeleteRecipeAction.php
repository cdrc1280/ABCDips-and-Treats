<?php

namespace App\Domains\Costing\Actions;

use App\Domains\Costing\Models\Recipe;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

final class DeleteRecipeAction
{
    public function execute(Recipe $recipe): void
    {
        if ($recipe->productionBatches()->exists()) {
            throw new DomainRuleViolation(__(':name has production batches and cannot be deleted.', ['name' => $recipe->name]));
        }

        DB::transaction(fn () => $recipe->delete());
    }
}
