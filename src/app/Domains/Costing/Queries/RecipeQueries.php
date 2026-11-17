<?php

namespace App\Domains\Costing\Queries;

use App\Domains\Costing\Models\Recipe;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class RecipeQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Recipe>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Recipe::query()
            ->search($filters->search)
            ->with(['product', 'recipeIngredients.ingredient', 'recipePackagings.packagingMaterial']);

        $this->applySort($query, $filters->sortField ?? 'name', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Recipe::class, 'name');
    }
}
