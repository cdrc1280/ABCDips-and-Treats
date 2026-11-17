<?php

namespace App\Domains\Inventory\Queries;

use App\Domains\Inventory\Models\Ingredient;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

final class IngredientQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, Ingredient>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = Ingredient::query()
            ->search($filters->search)
            ->with(['supplier'])
            ->withCount(['recipeIngredients']);

        $this->applySort($query, $filters->sortField ?? 'name', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(): array
    {
        return OptionsQuery::for(Ingredient::class, 'name');
    }
}
