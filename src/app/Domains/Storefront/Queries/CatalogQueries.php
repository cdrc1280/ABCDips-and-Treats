<?php

namespace App\Domains\Storefront\Queries;

use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Storefront\Data\CatalogFilterData;
use App\Domains\Storefront\Resources\ProductPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

/** Read side of the public catalog: listings, collections, product detail, categories. */
final class CatalogQueries
{
    public const PER_PAGE = 12;

    /**
     * @return Builder<Product>
     */
    private function base(): Builder
    {
        return Product::query()->with(['category', 'media'])->forCustomer()->withListingAggregates();
    }

    /** @return LengthAwarePaginator<int, array<string, mixed>> */
    public function paginate(CatalogFilterData $filters, int $perPage = self::PER_PAGE): LengthAwarePaginator
    {
        $query = $this->base();

        if ($filters->search) {
            $term = '%'.addcslashes($filters->search, '%_\\').'%';
            $query->where(fn (Builder $q) => $q->whereLike('name', $term)->orWhereLike('short_description', $term)->orWhereLike('sku', $term));
        }

        if ($filters->category) {
            $query->whereHas('category', fn (Builder $q) => $q->where('slug', $filters->category));
        }

        match ($filters->sort) {
            'popular' => $query->orderByDesc('order_items_sum_qty'),
            'rating_high' => $query->orderByDesc('approved_reviews_avg_rating'),
            'price_low' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_high' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name_asc' => $query->orderBy('name'),
            default => $query->latest(),
        };

        return $query->orderBy('id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Product $product) => ProductPresenter::card($product));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function collection(string $name, int $limit = 12): array
    {
        $query = $this->base();

        match ($name) {
            'featured' => $query->where('is_featured', true)->latest(),
            'best-sellers' => $query->where(fn (Builder $q) => $q->where('is_best_seller', true)->orWhereHas('orderItems'))
                ->orderByDesc('is_best_seller')->orderByDesc('order_items_sum_qty'),
            'new-arrivals' => $query->where(fn (Builder $q) => $q->where('is_new_arrival', true)->orWhere('created_at', '>=', now()->subDays(30)))
                ->orderByDesc('is_new_arrival')->latest(),
            default => $query->latest(),
        };

        return $query->take($limit)->get()->map(fn (Product $p) => ProductPresenter::card($p))->values()->all();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->base()->with(['tags', 'allergens', 'nutrition'])->where('slug', $slug)->first();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function related(Product $product, int $limit = 4): array
    {
        return $this->base()
            ->whereKeyNot($product->id)
            ->when($product->category_id, fn (Builder $q) => $q->where('category_id', $product->category_id))
            ->inRandomOrder()
            ->take($limit)
            ->get()
            ->map(fn (Product $p) => ProductPresenter::card($p))
            ->values()->all();
    }

    /**
     * Active categories that have at least one sellable product.
     *
     * @return array<int, array{name: string, slug: string, description: ?string, image_url: ?string, products_count: int}>
     */
    public function categories(): array
    {
        return Cache::remember('storefront.categories', 300, fn () => ProductCategory::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->forCustomer()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->filter(fn (ProductCategory $c) => $c->products_count > 0)
            ->map(fn (ProductCategory $c) => [
                'name' => $c->name,
                'slug' => $c->slug,
                'description' => $c->description,
                'image_url' => $c->image_url,
                'products_count' => (int) $c->products_count,
            ])
            ->values()
            ->all());
    }

    public function category(string $slug): ?ProductCategory
    {
        return ProductCategory::query()->where('is_active', true)->where('slug', $slug)->first();
    }
}
