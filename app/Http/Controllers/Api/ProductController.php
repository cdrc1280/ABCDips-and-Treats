<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'category',
            'tag',
            'featured',
            'best_seller',
            'new_arrival',
            'sort'
        ]);

        $perPage = (int) $request->get('per_page', 12);
        $products = $this->productService->getCatalogProducts($filters, $perPage);

        return ProductResource::collection($products)->response();
    }

    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->getProductBySlug($slug);

        if (!$product) {
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

    public function aboutStats(): JsonResponse
    {
        $customersCount = max(
            User::role('customer')->count(),
            Order::distinct('customer_email')->count()
        );

        $recipesCount = Product::forCustomer()->count();

        $avgRating = Review::where('is_approved', true)->avg('rating');
        $avgRatingFormatted = $avgRating ? number_format((float) $avgRating, 1) : '5.0';

        return response()->json([
            'happy_customers' => $customersCount,
            'signature_recipes' => $recipesCount,
            'average_rating' => $avgRatingFormatted,
        ]);
    }

    public function aboutContent(): JsonResponse
    {
        $defaultTimeline = [
            ['year' => '2020', 'emoji' => '🏠', 'title' => 'Home Kitchen Beginnings', 'desc' => 'ABCDips & Treats started in a small home kitchen, baking banana bread and cookies for friends and family.'],
            ['year' => '2021', 'emoji' => '❤️', 'title' => 'First Online Orders', 'desc' => 'Word spread and we started taking online orders through social media, quickly selling out every weekend.'],
            ['year' => '2023', 'emoji' => '🥰', 'title' => 'Full Menu & Delivery', 'desc' => 'Expanded to our full pastry menu including custom cakes, cheesecakes, and cinnamon rolls with city-wide delivery.'],
        ];

        $defaultValues = [
            ['emoji' => '🫖', 'title' => 'Quality Ingredients', 'desc' => 'We use only real creamery butter, imported Belgian chocolate, and fresh farm eggs. No shortcuts, ever.'],
            ['emoji' => '❤️', 'title' => 'Made with Love', 'desc' => 'Every pastry is handcrafted in small batches by our dedicated bakers who pour passion into every bite.'],
            ['emoji' => '🌟', 'title' => 'Community First', 'desc' => 'We believe in building relationships, supporting local suppliers, and making people smile one pastry at a time.'],
        ];

        return response()->json([
            'hero_tagline' => Setting::get('about_hero_tagline', 'our story'),
            'hero_title' => Setting::get('about_hero_title', "Baked with Heart,\nserved with love"),
            'hero_subtitle' => Setting::get('about_hero_subtitle', 'ABCDips & Treats began as a small home bakery with a simple dream: to share the joy of freshly baked, handcrafted pastries with every Filipino household.'),

            'timeline_tagline' => Setting::get('about_timeline_tagline', 'the journey'),
            'timeline_title' => Setting::get('about_timeline_title', 'The ABCDips Story'),
            'timeline' => Setting::getJson('about_timeline', $defaultTimeline),

            'values_tagline' => Setting::get('about_values_tagline', 'what drives us'),
            'values_title' => Setting::get('about_values_title', 'Our Core Values'),
            'values' => Setting::getJson('about_values', $defaultValues),

            'cta_tagline' => Setting::get('about_cta_tagline', 'ready to indulge?'),
            'cta_title' => Setting::get('about_cta_title', 'Order Your Favorites Today'),
            'cta_subtitle' => Setting::get('about_cta_subtitle', 'Same-day delivery available in Cavite. Fresh from our oven to your door.'),
            'cta_button_text' => Setting::get('about_cta_button_text', 'Browse Full Menu →'),
            'cta_button_url' => Setting::get('about_cta_button_url', '/shop'),
        ]);
    }

    public function homeContent(): JsonResponse
    {
        return response()->json([
            'hero_badge' => Setting::get('home_hero_badge', 'OVEN FRESH TODAY IN CAVITE'),
            'hero_title' => Setting::get('home_hero_title', 'Handcrafted Pastries'),
            'hero_subtitle' => Setting::get('home_hero_subtitle', 'baked with love & real butter'),
            'hero_description' => Setting::get('home_hero_des   cription', 'From our famous Classic Banana Bread Loaves and ultra-fudgy Belgian chocolate brownies to cheesecakes and fresh cinnamon rolls.'),
            'hero_btn_primary_text' => Setting::get('home_hero_btn_primary_text', 'Browse Full Menu'),
            'hero_btn_primary_url' => Setting::get('home_hero_btn_primary_url', '/shop'),
            'hero_btn_secondary_text' => Setting::get('home_hero_btn_secondary_text', 'Explore Best Sellers'),
            'hero_btn_secondary_url' => Setting::get('home_hero_btn_secondary_url', '/best-sellers'),
            'hero_bullet_1' => Setting::get('home_hero_bullet_1', 'Same-day & Scheduled Delivery'),
            'hero_bullet_2' => Setting::get('home_hero_bullet_2', '100% Real Creamery Butter'),
            'hero_card_badge' => Setting::get('home_hero_card_badge', 'Signature Treat'),
            'hero_card_title' => Setting::get('home_hero_card_title', 'Classic Banana Bread'),
            'hero_card_subtitle' => Setting::get('home_hero_card_subtitle', 'Starts at ₱280.00'),
            'hero_card_image' => $this->formatSettingImage(Setting::get('home_hero_card_image')),

            'spotlight_tagline' => Setting::get('home_spotlight_tagline', 'weekly special spotlight'),
            'spotlight_title' => Setting::get('home_spotlight_title', 'Signature Ube Cheesecake'),
            'spotlight_description' => Setting::get('home_spotlight_description', 'Real Philippine Ube Halaya folded into silky baked cream cheese set over a coconut Graham crust. Baked fresh in limited batches.'),
            'spotlight_btn_text' => Setting::get('home_spotlight_btn_text', 'Order Spotlight Treat — ₱680.00'),
            'spotlight_btn_url' => Setting::get('home_spotlight_btn_url', '/products/signature-ube-cheesecake-6-inch'),
            'spotlight_image' => $this->formatSettingImage(Setting::get('home_spotlight_image')),
        ]);
    }

    private function formatSettingImage(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, '/')) {
            return asset($path);
        }
        return asset('storage/' . ltrim($path, '/'));
    }
}
