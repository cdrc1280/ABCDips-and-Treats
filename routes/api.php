<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CustomOrderController;
use App\Http\Controllers\Api\PosController;
use App\Http\Controllers\Api\AnalyticsAndAiController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\PaymentController;

// ─── Auth Routes ──────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// ─── Product Catalog & Store Stats (Public) ───────────────────
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{slug}', [CategoryController::class, 'show']);

Route::get('products', [ProductController::class, 'index']);
Route::get('products/featured', [ProductController::class, 'featured']);
Route::get('products/best-sellers', [ProductController::class, 'bestSellers']);
Route::get('products/new-arrivals', [ProductController::class, 'newArrivals']);
Route::get('about-stats', [ProductController::class, 'aboutStats']);
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

// ─── Checkout, Tracking & Invoices (Public & Admin) ────────────────────
Route::post('checkout', [OrderController::class, 'checkout']);
Route::get('orders/track/{token}', [OrderController::class, 'track']);
Route::get('orders/{id}/invoice', [OrderController::class, 'adminInvoice']);
Route::get('orders/{id}/invoice/download', [OrderController::class, 'downloadInvoice']);

// ─── Delivery & Payment (Public) ─────────────────────────────────────
Route::post('delivery/quote', [DeliveryController::class, 'quote']);
Route::post('payments/create-source', [PaymentController::class, 'createSource']);
Route::get('payments/success', [PaymentController::class, 'success']);
Route::get('payments/failed', [PaymentController::class, 'failed']);
Route::get('settings/store', [PaymentController::class, 'storeSettings']);

// ─── Reviews & Custom Orders (Public) ─────────────────────────
Route::get('store/reviews', [ReviewController::class, 'storeReviews']);
Route::get('products/{id}/reviews', [ReviewController::class, 'index']);
Route::post('products/{id}/reviews', [ReviewController::class, 'store']);
Route::post('reviews/store-service', [ReviewController::class, 'store']);
Route::post('reviews/{id}/vote', [ReviewController::class, 'vote']);
Route::post('custom-orders', [CustomOrderController::class, 'store']);

// ─── POS & Analytics API ─────────────────────────────────────
Route::get('pos/products', [PosController::class, 'products']);
Route::post('pos/checkout', [PosController::class, 'checkout']);
Route::get('admin/analytics', [AnalyticsAndAiController::class, 'analytics']);
Route::post('admin/ai/query', [AnalyticsAndAiController::class, 'aiQuery']);

// ─── Authenticated User Routes ────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::get('user', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Profile & Credentials
    Route::get('customer/profile', [CustomerProfileController::class, 'show']);
    Route::put('customer/profile', [CustomerProfileController::class, 'updateProfile']);
    Route::post('customer/send-verification-email', [CustomerProfileController::class, 'sendVerificationEmail']);
    Route::post('customer/verify-email', [CustomerProfileController::class, 'verifyEmail']);
    Route::post('customer/send-phone-otp', [CustomerProfileController::class, 'sendPhoneOtp']);
    Route::post('customer/verify-phone-otp', [CustomerProfileController::class, 'verifyPhoneOtp']);
    Route::post('customer/password', [CustomerProfileController::class, 'changePassword']);
    Route::put('customer/password', [CustomerProfileController::class, 'changePassword']);
    Route::post('customer/avatar', [CustomerProfileController::class, 'updateAvatar']);

    // Customer Orders, Custom Orders & Wishlist
    Route::get('customer/orders', [OrderController::class, 'myOrders']);
    Route::get('customer/orders/{id}', [OrderController::class, 'show']);
    Route::post('customer/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('customer/custom-orders', [CustomOrderController::class, 'myCustomOrders']);
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist/{productId}', [WishlistController::class, 'toggle']);

    // Cart merge (post-login)
    Route::post('cart/merge', [CartController::class, 'merge']);
});

// ─── Contact Form (Public) ───────────────────────────────────
Route::post('contact', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:3000',
    ]);
    \App\Models\ContactMessage::create($data);
    return response()->json(['message' => 'Message sent successfully.'], 201);
});

// ─── Blog & Vlog Posts (Public) ──────────────────────────────
Route::prefix('blog')->group(function () {
    Route::get('posts', function () {
        try {
            $posts = \App\Models\BlogPost::with('author:id,name')
                ->where('status', 'published')
                ->orderByDesc('published_at')
                ->paginate(12);

            if ($posts->total() > 0) {
                return response()->json(['data' => $posts->items()]);
            }
        } catch (\Throwable $e) {
        }

        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'The Secret Behind Our Signature Banana Bread',
                    'slug' => 'secret-behind-our-signature-banana-bread',
                    'category' => 'Baking Tips',
                    'excerpt' => 'Discover how we use caramelized ripe bananas and pure creamery butter to achieve that unmatched moist texture.',
                    'content' => "At ABCDips & Treats, banana bread is not just a recipe; it is our foundation!\n\nWe select naturally ripened Cavendish bananas at peak sweetness, folding them with brown butter, cinnamon, and farm-fresh eggs before baking in small batches.\n\nPro Tip: Try serving warm with a scoop of vanilla ice cream or a drizzle of salted caramel!",
                    'published_at' => now()->subDays(2)->toIso8601String(),
                    'author' => ['name' => 'Chef Head Baker'],
                    'cover_image' => '/images/blog-banana-bread.jpg',
                ],
                [
                    'id' => 2,
                    'title' => 'How to Design Your Dream Custom Celebration Cake',
                    'slug' => 'how-to-design-your-dream-custom-celebration-cake',
                    'category' => 'Custom Cakes & Vlogs',
                    'excerpt' => 'A behind-the-scenes look at how we turn your theme inspirations into edible works of cake art.',
                    'content' => "Planning a wedding or 30th birthday?\n\nFrom choosing tiers and guest servings to selecting buttercream finishes and flavor pairings like Signature Ube Halaya or Dark Belgian Chocolate, here is your complete guide to ordering with ABCDips.\n\nOur head pastry chef works directly with your budget range and theme vision to create unforgettable celebration centerpieces.",
                    'published_at' => now()->subDays(5)->toIso8601String(),
                    'author' => ['name' => 'Pastry Design Team'],
                    'cover_image' => '/images/blog-custom-cake.jpg',
                ],
                [
                    'id' => 3,
                    'title' => '5 Pairing Ideas for Fudgy Dark Chocolate Brownies',
                    'slug' => '5-pairing-ideas-for-fudgy-dark-chocolate-brownies',
                    'category' => 'Treats & Pairings',
                    'excerpt' => 'Elevate your coffee break with these decadent brownie and dip combinations.',
                    'content' => "Our Belgian dark chocolate brownies feature crackly tops and dense, gooey centers.\n\nPair them with iced sea-salt latte, fresh strawberry compote, or melted dulce de leche for an indulgent afternoon treat!",
                    'published_at' => now()->subDays(8)->toIso8601String(),
                    'author' => ['name' => 'ABCDips Barista'],
                    'cover_image' => '/images/blog-brownies.jpg',
                ],
            ]
        ]);
    });

    Route::get('posts/{slug}', function ($slug) {
        try {
            $post = \App\Models\BlogPost::with('author:id,name')
                ->where('slug', $slug)
                ->where('status', 'published')
                ->first();

            if ($post) {
                return response()->json(['data' => $post]);
            }
        } catch (\Throwable $e) {
        }

        $defaults = [
            'secret-behind-our-signature-banana-bread' => [
                'id' => 1,
                'title' => 'The Secret Behind Our Signature Banana Bread',
                'slug' => 'secret-behind-our-signature-banana-bread',
                'category' => 'Baking Tips',
                'excerpt' => 'Discover how we use caramelized ripe bananas and pure creamery butter to achieve that unmatched moist texture.',
                'content' => "At ABCDips & Treats, banana bread is not just a recipe; it is our foundation!\n\nWe select naturally ripened Cavendish bananas at peak sweetness, folding them with brown butter, cinnamon, and farm-fresh eggs before baking in small batches.\n\nPro Tip: Try serving warm with a scoop of vanilla ice cream or a drizzle of salted caramel!",
                'published_at' => now()->subDays(2)->toIso8601String(),
                'author' => ['name' => 'Chef Head Baker'],
                'cover_image' => '/images/blog-banana-bread.jpg',
            ],
            'how-to-design-your-dream-custom-celebration-cake' => [
                'id' => 2,
                'title' => 'How to Design Your Dream Custom Celebration Cake',
                'slug' => 'how-to-design-your-dream-custom-celebration-cake',
                'category' => 'Custom Cakes & Vlogs',
                'excerpt' => 'A behind-the-scenes look at how we turn your theme inspirations into edible works of cake art.',
                'content' => "Planning a wedding or 30th birthday?\n\nFrom choosing tiers and guest servings to selecting buttercream finishes and flavor pairings like Signature Ube Halaya or Dark Belgian Chocolate, here is your complete guide to ordering with ABCDips.\n\nOur head pastry chef works directly with your budget range and theme vision to create unforgettable celebration centerpieces.",
                'published_at' => now()->subDays(5)->toIso8601String(),
                'author' => ['name' => 'Pastry Design Team'],
                'cover_image' => '/images/blog-custom-cake.jpg',
            ],
            '5-pairing-ideas-for-fudgy-dark-chocolate-brownies' => [
                'id' => 3,
                'title' => '5 Pairing Ideas for Fudgy Dark Chocolate Brownies',
                'slug' => '5-pairing-ideas-for-fudgy-dark-chocolate-brownies',
                'category' => 'Treats & Pairings',
                'excerpt' => 'Elevate your coffee break with these decadent brownie and dip combinations.',
                'content' => "Our Belgian dark chocolate brownies feature crackly tops and dense, gooey centers.\n\nPair them with iced sea-salt latte, fresh strawberry compote, or melted dulce de leche for an indulgent afternoon treat!",
                'published_at' => now()->subDays(8)->toIso8601String(),
                'author' => ['name' => 'ABCDips Barista'],
                'cover_image' => '/images/blog-brownies.jpg',
            ],
        ];

        $post = $defaults[$slug] ?? reset($defaults);

        return response()->json(['data' => $post]);
    });
});
