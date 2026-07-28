<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function getPaginated(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['category', 'tags', 'allergens', 'nutrition', 'media'])
            ->where('is_active', true);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category'])) {
            $catSlug = $filters['category'];
            $query->whereHas('category', fn ($q) => $q->where('slug', $catSlug));
        }

        if (!empty($filters['tag'])) {
            $tagSlug = $filters['tag'];
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        if (!empty($filters['featured'])) {
            $query->where('is_featured', true);
        }

        if (!empty($filters['best_seller'])) {
            $query->where('is_best_seller', true);
        }

        if (!empty($filters['new_arrival'])) {
            $query->where('is_new_arrival', true);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'rating_high':
                $query->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating')
                      ->orderByDesc('reviews_avg_rating');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::query()
            ->with(['category', 'tags', 'allergens', 'nutrition', 'media'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return Product::query()
            ->with(['category', 'tags', 'media'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take($limit)
            ->get();
    }

    public function getBestSellers(int $limit = 6): Collection
    {
        return Product::query()
            ->with(['category', 'tags', 'media'])
            ->where('is_active', true)
            ->where('is_best_seller', true)
            ->take($limit)
            ->get();
    }

    public function getNewArrivals(int $limit = 6): Collection
    {
        return Product::query()
            ->with(['category', 'tags', 'media'])
            ->where('is_active', true)
            ->where('is_new_arrival', true)
            ->take($limit)
            ->get();
    }
}
