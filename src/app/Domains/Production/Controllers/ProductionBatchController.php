<?php

namespace App\Domains\Production\Controllers;

use App\Domains\Catalog\Models\Product;
use App\Domains\Costing\Models\Recipe;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Production\Actions\CompleteProductionBatchAction;
use App\Domains\Production\Actions\CreateProductionBatchAction;
use App\Domains\Production\Actions\DeleteProductionBatchAction;
use App\Domains\Production\Actions\UpdateProductionBatchAction;
use App\Domains\Production\Data\ProductionBatchData;
use App\Domains\Production\Enums\ProductionBatchPermission;
use App\Domains\Production\Models\ProductionBatch;
use App\Domains\Production\Queries\ProductionBatchQueries;
use App\Domains\Production\Queries\ProductionBatchSummaryQuery;
use App\Domains\Production\Resources\ProductionBatchResource;
use App\Domains\User\Models\User;
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

class ProductionBatchController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.ProductionBatchPermission::ProductionBatchesView->value, only: ['index']),
            new Middleware('can:'.ProductionBatchPermission::ProductionBatchesCreate->value, only: ['create', 'store']),
            new Middleware('can:'.ProductionBatchPermission::ProductionBatchesEdit->value, only: ['edit', 'update', 'complete']),
            new Middleware('can:'.ProductionBatchPermission::ProductionBatchesDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, ProductionBatchQueries $queries, ProductionBatchSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'production_batches', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (ProductionBatch $productionBatch) => ProductionBatchResource::fromModel($productionBatch));

        return Inertia::render('production-batch/index', [
            'records' => ProductionBatchResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
        ]);
    }

    #[FormRules(ProductionBatchData::class)]
    public function create(): Response
    {
        return Inertia::render('production-batch/create', [
            'recipe_options' => fn () => OptionsQuery::for(Recipe::class),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
            'baker_options' => fn () => OptionsQuery::for(User::class, 'name', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer')), 'email'),
        ]);
    }

    public function store(ProductionBatchData $data, CreateProductionBatchAction $create): RedirectResponse
    {
        $productionBatch = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $productionBatch->batch_number])]);

        return to_route('admin.production-batches.index');
    }

    #[FormRules(ProductionBatchData::class)]
    public function edit(ProductionBatch $productionBatch, ProductionBatchQueries $queries): Response
    {
        return Inertia::render('production-batch/edit', [
            'record' => ProductionBatchResource::fromModel($productionBatch),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'recipe_options' => fn () => OptionsQuery::for(Recipe::class),
            'product_options' => fn () => OptionsQuery::for(Product::class, 'name', null, 'sku'),
            'baker_options' => fn () => OptionsQuery::for(User::class, 'name', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', '!=', 'customer')), 'email'),
        ]);
    }

    public function update(ProductionBatch $productionBatch, ProductionBatchData $data, UpdateProductionBatchAction $update): RedirectResponse
    {
        $update->execute($productionBatch, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $productionBatch->batch_number])]);

        return to_route('admin.production-batches.index');
    }

    /** Complete batch. */
    public function complete(ProductionBatch $productionBatch, CompleteProductionBatchAction $action): RedirectResponse
    {
        $action->execute($productionBatch);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Batch :number completed — stock updated.', ['number' => $productionBatch->batch_number])]);

        return back();
    }

    public function destroy(ProductionBatch $productionBatch, DeleteProductionBatchAction $delete): RedirectResponse
    {
        $delete->execute($productionBatch);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $productionBatch->batch_number])]);

        return back();
    }
}
