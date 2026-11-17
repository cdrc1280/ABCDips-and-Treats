<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Sales\Actions\CreateDeliveryPoolAction;
use App\Domains\Sales\Actions\DeleteDeliveryPoolAction;
use App\Domains\Sales\Actions\SettleDeliveryPoolAction;
use App\Domains\Sales\Actions\UpdateDeliveryPoolAction;
use App\Domains\Sales\Data\DeliveryPoolData;
use App\Domains\Sales\Enums\DeliveryPoolPermission;
use App\Domains\Sales\Models\DeliveryPool;
use App\Domains\Sales\Queries\DeliveryPoolQueries;
use App\Domains\Sales\Queries\DeliveryPoolSummaryQuery;
use App\Domains\Sales\Resources\DeliveryPoolResource;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class DeliveryPoolController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.DeliveryPoolPermission::DeliveryPoolsView->value, only: ['index']),
            new Middleware('can:'.DeliveryPoolPermission::DeliveryPoolsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.DeliveryPoolPermission::DeliveryPoolsEdit->value, only: ['edit', 'update', 'settle']),
            new Middleware('can:'.DeliveryPoolPermission::DeliveryPoolsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, DeliveryPoolQueries $queries, DeliveryPoolSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'delivery_pools', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (DeliveryPool $deliveryPool) => DeliveryPoolResource::fromModel($deliveryPool));

        return Inertia::render('delivery-pool/index', [
            'records' => DeliveryPoolResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(DeliveryPoolData::class)]
    public function create(): Response
    {
        return Inertia::render('delivery-pool/create', [
        ]);
    }

    public function store(DeliveryPoolData $data, CreateDeliveryPoolAction $create): RedirectResponse
    {
        $deliveryPool = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $deliveryPool->pool_code])]);

        return to_route('admin.delivery-pools.index');
    }

    #[FormRules(DeliveryPoolData::class)]
    public function edit(DeliveryPool $deliveryPool, DeliveryPoolQueries $queries): Response
    {
        return Inertia::render('delivery-pool/edit', [
            'record' => DeliveryPoolResource::fromModel($deliveryPool->load('orders')->loadCount('orders')),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(DeliveryPool $deliveryPool, DeliveryPoolData $data, UpdateDeliveryPoolAction $update): RedirectResponse
    {
        $update->execute($deliveryPool, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $deliveryPool->pool_code])]);

        return to_route('admin.delivery-pools.index');
    }

    /** Settle pool. */
    public function settle(DeliveryPool $deliveryPool, SettleDeliveryPoolAction $action): RedirectResponse
    {
        $action->execute($deliveryPool);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Pool :code settled for :count orders.', ['code' => $deliveryPool->pool_code, 'count' => $deliveryPool->orders()->count()])]);

        return back();
    }

    public function destroy(DeliveryPool $deliveryPool, DeleteDeliveryPoolAction $delete): RedirectResponse
    {
        $delete->execute($deliveryPool);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $deliveryPool->pool_code])]);

        return back();
    }
}
