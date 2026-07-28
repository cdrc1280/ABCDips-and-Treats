<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $wishlists = Wishlist::where('user_id', $user->id)
            ->with(['product.category', 'product.media'])
            ->get();

        $products = $wishlists->map(fn ($w) => $w->product)->filter();

        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }

    public function toggle(Request $request, int $productId): JsonResponse
    {
        $user = $request->user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message' => 'Removed from wishlist.',
                'added'   => false,
            ]);
        }

        Wishlist::create([
            'user_id'    => $user->id,
            'product_id' => $productId,
        ]);

        return response()->json([
            'message' => 'Added to wishlist!',
            'added'   => true,
        ]);
    }
}
