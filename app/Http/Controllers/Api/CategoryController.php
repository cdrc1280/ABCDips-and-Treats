<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = ProductCategory::query()
            ->withCount('products')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => ProductCategoryResource::collection($categories),
        ])->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=600');
    }

    public function show(string $slug): JsonResponse
    {
        $category = ProductCategory::query()
            ->withCount('products')
            ->where('is_active', true)
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        return response()->json([
            'data' => new ProductCategoryResource($category),
        ]);
    }
}
