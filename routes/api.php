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
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SuggestionController;
use App\Http\Controllers\Api\PublicAiController;
use App\Http\Controllers\Api\ChatEscalationController;
use App\Http\Controllers\Api\OtpController;

// ─── Auth Routes ──────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:8,1');
});

// ─── Product Catalog & Store Stats (Public) ───────────────────
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{slug}', [CategoryController::class, 'show']);

Route::get('products', [ProductController::class, 'index']);
Route::get('products/featured', [ProductController::class, 'featured']);
Route::get('products/best-sellers', [ProductController::class, 'bestSellers']);
Route::get('products/new-arrivals', [ProductController::class, 'newArrivals']);
Route::get('about-stats', [ProductController::class, 'aboutStats']);
Route::get('about-content', [ProductController::class, 'aboutContent']);
Route::get('home-content', [ProductController::class, 'homeContent']);
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
Route::post('checkout', [OrderController::class, 'checkout'])->middleware('throttle:15,1');
Route::get('orders/track/{token}', [OrderController::class, 'track']);

// ─── Delivery & Payment (Public) ─────────────────────────────────────
Route::post('delivery/quote', [DeliveryController::class, 'quote'])->middleware('throttle:20,1');
Route::post('payments/create-source', [PaymentController::class, 'createSource'])->middleware('throttle:20,1');
Route::get('payments/success', [PaymentController::class, 'success']);
Route::get('payments/failed', [PaymentController::class, 'failed']);
Route::get('settings/store', [PaymentController::class, 'storeSettings']);

// ─── Reviews & Custom Orders (Public) ─────────────────────────
Route::get('store/reviews', [ReviewController::class, 'storeReviews']);
Route::get('products/{id}/reviews', [ReviewController::class, 'index']);
Route::post('products/{id}/reviews', [ReviewController::class, 'store'])->middleware('throttle:15,1');
Route::post('reviews/store-service', [ReviewController::class, 'store'])->middleware('throttle:15,1');
Route::post('reviews/{id}/vote', [ReviewController::class, 'vote'])->middleware('throttle:25,1');
Route::post('custom-orders', [CustomOrderController::class, 'store'])->middleware('throttle:12,1');

// ─── Public AI Chat (Customer-Facing) ────────────────────────
Route::post('ai/query', [PublicAiController::class, 'query'])->middleware('throttle:20,1');
Route::post('chat/escalate', [ChatEscalationController::class, 'store'])->middleware('throttle:15,1');
Route::get('chat/escalate/messages', [ChatEscalationController::class, 'fetchClientConversation']);

// ─── POS, Analytics & AI Admin API ───────────────────────────
Route::middleware(['auth:sanctum', 'role:super_admin|admin'])->group(function () {
    Route::get('pos/products', [PosController::class, 'products']);
    Route::post('pos/checkout', [PosController::class, 'checkout']);
    Route::get('admin/analytics', [AnalyticsAndAiController::class, 'analytics']);
    Route::post('admin/ai/query', [AnalyticsAndAiController::class, 'aiQuery'])->middleware('throttle:30,1');
});

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
    Route::post('customer/send-phone-otp', [CustomerProfileController::class, 'sendPhoneOtp'])->middleware('throttle:5,1');
    Route::post('customer/verify-phone-otp', [CustomerProfileController::class, 'verifyPhoneOtp'])->middleware('throttle:3,1');
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

    // Email OTP Verification (6-digit code)
    Route::post('otp/email/send', [OtpController::class, 'sendEmailOtp'])->middleware('throttle:3,10');
    Route::post('otp/email/verify', [OtpController::class, 'verifyEmailOtp'])->middleware('throttle:10,1');
});

// ─── Contact Form (Public) ───────────────────────────────────
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:12,1');

// ─── Suggestions (Public) ──────────────────────────────────────
Route::post('suggestions', [SuggestionController::class, 'store'])->middleware('throttle:5,1');

// ─── Blog & Vlog Posts (Public) ──────────────────────────────
Route::prefix('blog')->group(function () {
    Route::get('posts', [BlogController::class, 'index']);
    Route::get('posts/{slug}', [BlogController::class, 'show']);
});

// ─── API Fallback (404 JSON) ──────────────────────────────────
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found. Please verify the request URL and HTTP method.',
        'status'  => 404,
    ], 404);
});
