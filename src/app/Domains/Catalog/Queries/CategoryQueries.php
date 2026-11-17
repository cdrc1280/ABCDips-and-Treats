<?php

namespace App\Domains\Catalog\Queries;

use App\Domains\Catalog\Models\ProductCategory;
use App\Shared\Data\FilterData;
use App\Shared\Queries\Concerns\SortsQueries;
use Illuminate\Pagination\LengthAwarePaginator;

final class CategoryQueries
{
    use SortsQueries;

    /**
     * @return LengthAwarePaginator<int, ProductCategory>
     */
    public function paginate(FilterData $filters): LengthAwarePaginator
    {
        $query = ProductCategory::query()
            ->search($filters->search)
            ->withCount('products');

        $this->applySort($query, $filters->sortField ?? 'sort_order', $filters->sortDir ?? 'asc');

        return $query->paginate($filters->perPage, ['*'], 'page', $filters->page);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function getAllAsOptions(bool $activeOnly = false): array
    {
        return ProductCategory::query()
            ->when($activeOnly, fn ($q) => $q->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (ProductCategory $category) => ['label' => $category->name, 'value' => $category->id])
            ->values()->all();
    }
}
