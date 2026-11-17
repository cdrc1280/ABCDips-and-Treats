<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Sales\Actions\CompleteCustomOrderAction;
use App\Domains\Sales\Actions\CreateCustomOrderAction;
use App\Domains\Sales\Actions\DeleteCustomOrderAction;
use App\Domains\Sales\Actions\StartProductionCustomOrderAction;
use App\Domains\Sales\Actions\UpdateCustomOrderAction;
use App\Domains\Sales\Data\CustomOrderData;
use App\Domains\Sales\Enums\CustomOrderPermission;
use App\Domains\Sales\Models\CustomOrder;
use App\Domains\Sales\Queries\CustomOrderQueries;
use App\Domains\Sales\Queries\CustomOrderSummaryQuery;
use App\Domains\Sales\Resources\CustomOrderResource;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class CustomOrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.CustomOrderPermission::CustomOrdersView->value, only: ['index']),
            new Middleware('can:'.CustomOrderPermission::CustomOrdersCreate->value, only: ['create', 'store']),
            new Middleware('can:'.CustomOrderPermission::CustomOrdersEdit->value, only: ['edit', 'update', 'startProduction', 'complete']),
            new Middleware('can:'.CustomOrderPermission::CustomOrdersDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, CustomOrderQueries $queries, CustomOrderSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'custom_orders', ['sort_field' => 'event_date', 'sort_dir' => 'asc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (CustomOrder $customOrder) => CustomOrderResource::fromModel($customOrder));

        return Inertia::render('custom-order/index', [
            'records' => CustomOrderResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(CustomOrderData::class)]
    public function create(): Response
    {
        return Inertia::render('custom-order/create', [
        ]);
    }

    public function store(CustomOrderData $data, CreateCustomOrderAction $create): RedirectResponse
    {
        $customOrder = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $customOrder->reference_number])]);

        return to_route('admin.custom-orders.index');
    }

    #[FormRules(CustomOrderData::class)]
    public function edit(CustomOrder $customOrder, CustomOrderQueries $queries): Response
    {
        return Inertia::render('custom-order/edit', [
            'record' => CustomOrderResource::fromModel($customOrder),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(CustomOrder $customOrder, CustomOrderData $data, UpdateCustomOrderAction $update): RedirectResponse
    {
        $update->execute($customOrder, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $customOrder->reference_number])]);

        return to_route('admin.custom-orders.index');
    }

    /** Start production. */
    public function startProduction(CustomOrder $customOrder, StartProductionCustomOrderAction $action): RedirectResponse
    {
        $action->execute($customOrder);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':ref is now in production.', ['ref' => $customOrder->reference_number])]);

        return back();
    }

    /** Mark completed. */
    public function complete(CustomOrder $customOrder, CompleteCustomOrderAction $action): RedirectResponse
    {
        $action->execute($customOrder);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':ref marked as completed.', ['ref' => $customOrder->reference_number])]);

        return back();
    }

    public function destroy(CustomOrder $customOrder, DeleteCustomOrderAction $delete): RedirectResponse
    {
        $delete->execute($customOrder);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $customOrder->reference_number])]);

        return back();
    }
}
