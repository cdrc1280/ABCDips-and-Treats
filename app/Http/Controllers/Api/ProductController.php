<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search', 'category', 'tag', 'featured', 'best_seller', 'new_arrival', 'sort'
        ]);

        $perPage = (int) $request->get('per_page', 12);
        $products = $this->productService->getCatalogProducts($filters, $perPage);

        return ProductResource::collection($products)->response();
    }

    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->getProductBySlug($slug);

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json(['data' => new ProductResource($product)]);
    }

    public function featured(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 6);
        $products = $this->productService->getFeaturedProducts($limit);

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function bestSellers(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 6);
        $products = $this->productService->getBestSellerProducts($limit);

        return response()->json(['data' => ProductResource::collection($products)]);
    }

    public function newArrivals(Request $request): JsonResponse
    {
        $limit = (int) $request->get('limit', 6);
        $products = $this->productService->getNewArrivalProducts($limit);

        return response()->json(['data' => ProductResource::collection($products)]);
    }
}
