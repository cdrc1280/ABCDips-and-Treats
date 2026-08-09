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
            ->forCustomer();

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
            $query->withSum('orderItems', 'qty')
                  ->orderByDesc('order_items_sum_qty')
                  ->orderByDesc('is_best_seller');
        }

        if (!empty($filters['new_arrival'])) {
            $query->where('created_at', '>=', now()->subDays(30));
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
                if (empty($filters['best_seller'])) {
                    $query->latest();
                }
                break;
        }

        return $query->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::query()
            ->with(['category', 'tags', 'allergens', 'nutrition', 'media'])
            ->forCustomer()
            ->where('slug', $slug)
            ->first();
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return Product::query()
            ->with(['category', 'tags', 'media'])
            ->forCustomer()
            ->where('is_featured', true)
            ->take($limit)
            ->get();
    }

    public function getBestSellers(int $limit = 6): Collection
    {
        return Product::query()
            ->with(['category', 'tags', 'media'])
            ->withSum('orderItems', 'qty')
            ->forCustomer()
            ->where(function ($q) {
                $q->where('is_best_seller', true)
                  ->orWhereHas('orderItems');
            })
            ->orderByDesc('is_best_seller')
            ->orderByDesc('order_items_sum_qty')
            ->take($limit)
            ->get();
    }

    public function getNewArrivals(int $limit = 6): Collection
    {
        return Product::query()
            ->with(['category', 'tags', 'media'])
            ->forCustomer()
            ->where(function ($q) {
                $q->where('is_new_arrival', true)
                  ->orWhere('created_at', '>=', now()->subDays(30));
            })
            ->orderByDesc('is_new_arrival')
            ->latest()
            ->take($limit)
            ->get();
    }
}
