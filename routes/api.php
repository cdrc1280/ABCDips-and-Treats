<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;

// ─── Auth Routes ──────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// ─── Product Catalog (Public) ──────────────────────────────────
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{slug}', [CategoryController::class, 'show']);

Route::get('products', [ProductController::class, 'index']);
Route::get('products/featured', [ProductController::class, 'featured']);
Route::get('products/best-sellers', [ProductController::class, 'bestSellers']);
Route::get('products/new-arrivals', [ProductController::class, 'newArrivals']);
Route::get('products/{slug}', [ProductController::class, 'show']);

// ─── Cart REST Endpoints (Public/Guest & Auth) ────────────────
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'show']);
    Route::post('items', [CartController::class, 'addItem']);
    Route::put('items/{id}', [CartController::class, 'updateItem']);
    Route::delete('items/{id}', [CartController::class, 'removeItem']);
    Route::post('items/{id}/restore', [CartController::class, 'restoreItem']);
    Route::post('batch', [CartController::class, 'batch']);
    Route::post('coupon', [CartController::class, 'applyCoupon']);
    Route::delete('coupon', [CartController::class, 'removeCoupon']);
});

// ─── Checkout & Tracking (Public) ────────────────────────────
Route::post('checkout', [OrderController::class, 'checkout']);
Route::get('orders/track/{token}', [OrderController::class, 'track']);

// ─── Reviews & Custom Orders (Public) ─────────────────────────
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CustomOrderController;

Route::get('products/{id}/reviews', [ReviewController::class, 'index']);
Route::post('products/{id}/reviews', [ReviewController::class, 'store']);
Route::post('reviews/{id}/vote', [ReviewController::class, 'vote']);
Route::post('custom-orders', [CustomOrderController::class, 'store']);

// ─── POS & Analytics API ─────────────────────────────────────
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\AnalyticsAndAiController;

Route::get('pos/products', [PosController::class, 'products']);
Route::post('pos/checkout', [PosController::class, 'checkout']);
Route::get('admin/analytics', [AnalyticsAndAiController::class, 'analytics']);
Route::post('admin/ai/query', [AnalyticsAndAiController::class, 'aiQuery']);


// ─── Authenticated User Routes ────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Profile & Credentials
    Route::put('customer/profile', [CustomerProfileController::class, 'updateProfile']);
    Route::post('customer/password', [CustomerProfileController::class, 'changePassword']);
    Route::post('customer/avatar', [CustomerProfileController::class, 'updateAvatar']);

    // Customer Orders, Custom Orders & Wishlist
    Route::get('customer/orders', [OrderController::class, 'myOrders']);
    Route::get('customer/custom-orders', [CustomOrderController::class, 'myCustomOrders']);
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist/{productId}', [WishlistController::class, 'toggle']);

    // Cart merge (post-login)
    Route::post('cart/merge', [CartController::class, 'merge']);
});

// ─── Contact Form (Public) ───────────────────────────────────
Route::post('contact', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'name'    => 'required|string|max:100',
        'email'   => 'required|email|max:255',
        'phone'   => 'nullable|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:3000',
    ]);
    \App\Models\ContactMessage::create($data);
    return response()->json(['message' => 'Message sent successfully.'], 201);
});

// ─── Blog Posts (Public) ─────────────────────────────────────
Route::prefix('blog')->group(function () {
    Route::get('posts', function () {
        $posts = \App\Models\BlogPost::with('author:id,name')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);
        return response()->json(['data' => $posts->items()]);
    });
    Route::get('posts/{slug}', function ($slug) {
        $post = \App\Models\BlogPost::with('author:id,name')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        return response()->json(['data' => $post]);
    });
});

