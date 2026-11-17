<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Storefront\Data\CatalogFilterData;
use App\Domains\Storefront\Queries\CatalogQueries;
use App\Domains\Storefront\Queries\StorefrontContent;
use App\Domains\Storefront\Resources\ProductPresenter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CatalogController extends Controller
{
    private const COLLECTIONS = [
        'featured' => ['title' => "Baker's picks", 'description' => "What we'd order this week."],
        'best-sellers' => ['title' => 'Best sellers', 'description' => 'What people order again and again.'],
        'new-arrivals' => ['title' => 'New on the menu', 'description' => 'The latest additions.'],
    ];

    public function __construct(private readonly CatalogQueries $catalog) {}

    public function home(StorefrontContent $content, ReviewService $reviews): Response
    {
        return Inertia::render('storefront/home', [
            'content' => $content->home(),
            'featured' => fn () => $this->catalog->collection('featured', 8),
            'bestSellers' => fn () => $this->catalog->collection('best-sellers', 8),
            'newArrivals' => fn () => $this->catalog->collection('new-arrivals', 8),
            'categories' => fn () => $this->catalog->categories(),
            'storeReviews' => Inertia::defer(fn () => $reviews->getStoreServiceReviews()),
        ]);
    }

    public function shop(Request $request): Response
    {
        $filters = CatalogFilterData::fromQuery($request->query());

        return Inertia::render('storefront/shop', [
            'filters' => $filters->toArray(),
            'products' => Inertia::scroll(fn () => $this->catalog->paginate($filters)),
            'categories' => fn () => $this->catalog->categories(),
        ]);
    }

    public function category(Request $request, string $slug): Response
    {
        $category = $this->catalog->category($slug);
        abort_if($category === null, 404);

        $filters = CatalogFilterData::fromQuery([...$request->query(), 'category' => $slug]);

        return Inertia::render('storefront/category', [
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image_url' => $category->image_url,
            ],
            'filters' => $filters->toArray(),
            'products' => Inertia::scroll(fn () => $this->catalog->paginate($filters)),
        ]);
    }

    public function collection(string $collection): Response
    {
        abort_unless(isset(self::COLLECTIONS[$collection]), 404);

        return Inertia::render('storefront/collection', [
            'collection' => ['key' => $collection, ...self::COLLECTIONS[$collection]],
            'products' => $this->catalog->collection($collection, 24),
        ]);
    }

    public function show(string $slug, ReviewService $reviews): Response
    {
        $product = $this->catalog->findBySlug($slug);
        abort_if($product === null, 404);

        return Inertia::render('storefront/product', [
            'product' => ProductPresenter::detail($product),
            'related' => Inertia::defer(fn () => $this->catalog->related($product)),
            'reviews' => Inertia::defer(fn () => ReviewResource::collection($reviews->getApprovedProductReviews($product->id, 20))->resolve(), 'reviews'),
        ]);
    }

    /** Product detail for the quick-view dialog (opened from listings without leaving the page). */
    public function quickView(string $slug): JsonResponse
    {
        $product = $this->catalog->findBySlug($slug);
        abort_if($product === null, 404);

        return response()->json(ProductPresenter::detail($product));
    }
}
