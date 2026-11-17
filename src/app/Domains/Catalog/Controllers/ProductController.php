<?php

namespace App\Domains\Catalog\Controllers;

use App\Domains\Catalog\Actions\CreateProductAction;
use App\Domains\Catalog\Actions\DeleteProductAction;
use App\Domains\Catalog\Actions\UpdateProductAction;
use App\Domains\Catalog\Data\ProductData;
use App\Domains\Catalog\Enums\ProductPermission;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\ProductCategory;
use App\Domains\Catalog\Queries\ProductQueries;
use App\Domains\Catalog\Queries\ProductSummaryQuery;
use App\Domains\Catalog\Resources\ProductResource;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use App\Shared\Queries\OptionsQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.ProductPermission::ProductsView->value, only: ['index']),
            new Middleware('can:'.ProductPermission::ProductsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.ProductPermission::ProductsEdit->value, only: ['edit', 'update', 'toggle']),
            new Middleware('can:'.ProductPermission::ProductsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, ProductQueries $queries, ProductSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'products', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Product $product) => ProductResource::fromModel($product));

        return Inertia::render('product/index', [
            'records' => ProductResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
            'category_options' => fn () => OptionsQuery::for(ProductCategory::class),
        ]);
    }

    #[FormRules(ProductData::class)]
    public function create(): Response
    {
        return Inertia::render('product/create', [
            'category_options' => fn () => OptionsQuery::for(ProductCategory::class),
        ]);
    }

    public function store(ProductData $data, CreateProductAction $create): RedirectResponse
    {
        $product = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $product->name])]);

        return to_route('admin.products.index');
    }

    #[FormRules(ProductData::class)]
    public function edit(Product $product, ProductQueries $queries): Response
    {
        return Inertia::render('product/edit', [
            'record' => ProductResource::fromModel($product->load(['category', 'tags'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'category_options' => fn () => OptionsQuery::for(ProductCategory::class),
        ]);
    }

    public function update(Product $product, ProductData $data, UpdateProductAction $update): RedirectResponse
    {
        $update->execute($product, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $product->name])]);

        return to_route('admin.products.index');
    }

    /** Inline switch on the index table. */
    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $product->update(['is_active' => $request->boolean('is_active')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $product->is_active
            ? __(':name is now active.', ['name' => $product->name])
            : __(':name is now inactive.', ['name' => $product->name])]);

        return back();
    }

    public function destroy(Product $product, DeleteProductAction $delete): RedirectResponse
    {
        $delete->execute($product);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $product->name])]);

        return back();
    }
}
