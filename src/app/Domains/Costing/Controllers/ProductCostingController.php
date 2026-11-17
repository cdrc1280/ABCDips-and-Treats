<?php

namespace App\Domains\Costing\Controllers;

use App\Domains\Catalog\Models\Product;
use App\Domains\Costing\Actions\CreateProductCostingAction;
use App\Domains\Costing\Actions\DeleteProductCostingAction;
use App\Domains\Costing\Actions\UpdateProductCostingAction;
use App\Domains\Costing\Data\ProductCostingData;
use App\Domains\Costing\Enums\ProductCostingPermission;
use App\Domains\Costing\Models\ProductCosting;
use App\Domains\Costing\Queries\ProductCostingQueries;
use App\Domains\Costing\Resources\ProductCostingResource;
use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\PackagingMaterial;
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

class ProductCostingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.ProductCostingPermission::ProductCostingsView->value, only: ['index']),
            new Middleware('can:'.ProductCostingPermission::ProductCostingsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.ProductCostingPermission::ProductCostingsEdit->value, only: ['edit', 'update']),
            new Middleware('can:'.ProductCostingPermission::ProductCostingsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, ProductCostingQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'product_costings', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (ProductCosting $productCosting) => ProductCostingResource::fromModel($productCosting));

        return Inertia::render('product-costing/index', [
            'records' => ProductCostingResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(ProductCostingData::class)]
    public function create(): Response
    {
        return Inertia::render('product-costing/create', [
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
            'packaging_options' => fn () => OptionsQuery::for(PackagingMaterial::class, 'name', fn ($q) => $q->where('is_active', true), 'unit'),
        ]);
    }

    public function store(ProductCostingData $data, CreateProductCostingAction $create): RedirectResponse
    {
        $productCosting = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $productCosting->name])]);

        return to_route('admin.product-costings.index');
    }

    #[FormRules(ProductCostingData::class)]
    public function edit(ProductCosting $productCosting, ProductCostingQueries $queries): Response
    {
        return Inertia::render('product-costing/edit', [
            'record' => ProductCostingResource::fromModel($productCosting->load(['product', 'costingItems.ingredient', 'costingItems.packagingMaterial'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
            'packaging_options' => fn () => OptionsQuery::for(PackagingMaterial::class, 'name', fn ($q) => $q->where('is_active', true), 'unit'),
        ]);
    }

    public function update(ProductCosting $productCosting, ProductCostingData $data, UpdateProductCostingAction $update): RedirectResponse
    {
        $update->execute($productCosting, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $productCosting->name])]);

        return to_route('admin.product-costings.index');
    }

    public function destroy(ProductCosting $productCosting, DeleteProductCostingAction $delete): RedirectResponse
    {
        $delete->execute($productCosting);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $productCosting->name])]);

        return back();
    }
}
