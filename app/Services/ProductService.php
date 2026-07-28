<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function getCatalogProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    public function getProductBySlug(string $slug): ?Product
    {
        return $this->repository->findBySlug($slug);
    }

    public function getFeaturedProducts(int $limit = 6): Collection
    {
        return $this->repository->getFeatured($limit);
    }

    public function getBestSellerProducts(int $limit = 6): Collection
    {
        return $this->repository->getBestSellers($limit);
    }

    public function getNewArrivalProducts(int $limit = 6): Collection
    {
        return $this->repository->getNewArrivals($limit);
    }
}
