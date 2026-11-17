<?php

namespace App\Domains\Purchasing\Controllers;

use App\Domains\Inventory\Models\Ingredient;
use App\Domains\Inventory\Models\Supplier;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Purchasing\Actions\CreatePurchaseOrderAction;
use App\Domains\Purchasing\Actions\DeletePurchaseOrderAction;
use App\Domains\Purchasing\Actions\ReceivePurchaseOrderAction;
use App\Domains\Purchasing\Actions\UpdatePurchaseOrderAction;
use App\Domains\Purchasing\Data\PurchaseOrderData;
use App\Domains\Purchasing\Enums\PurchaseOrderPermission;
use App\Domains\Purchasing\Models\PurchaseOrder;
use App\Domains\Purchasing\Queries\PurchaseOrderQueries;
use App\Domains\Purchasing\Queries\PurchaseOrderSummaryQuery;
use App\Domains\Purchasing\Resources\PurchaseOrderResource;
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

class PurchaseOrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.PurchaseOrderPermission::PurchaseOrdersView->value, only: ['index']),
            new Middleware('can:'.PurchaseOrderPermission::PurchaseOrdersCreate->value, only: ['create', 'store']),
            new Middleware('can:'.PurchaseOrderPermission::PurchaseOrdersEdit->value, only: ['edit', 'update', 'receive']),
            new Middleware('can:'.PurchaseOrderPermission::PurchaseOrdersDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, PurchaseOrderQueries $queries, PurchaseOrderSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'purchase_orders', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (PurchaseOrder $purchaseOrder) => PurchaseOrderResource::fromModel($purchaseOrder));

        return Inertia::render('purchase-order/index', [
            'records' => PurchaseOrderResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
        ]);
    }

    #[FormRules(PurchaseOrderData::class)]
    public function create(): Response
    {
        return Inertia::render('purchase-order/create', [
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
        ]);
    }

    public function store(PurchaseOrderData $data, CreatePurchaseOrderAction $create): RedirectResponse
    {
        $purchaseOrder = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $purchaseOrder->po_number])]);

        return to_route('admin.purchase-orders.index');
    }

    #[FormRules(PurchaseOrderData::class)]
    public function edit(PurchaseOrder $purchaseOrder, PurchaseOrderQueries $queries): Response
    {
        return Inertia::render('purchase-order/edit', [
            'record' => PurchaseOrderResource::fromModel($purchaseOrder->load(['supplier', 'items.ingredient'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
            'supplier_options' => fn () => OptionsQuery::for(Supplier::class),
            'ingredient_options' => fn () => OptionsQuery::for(Ingredient::class, 'name', null, 'unit'),
        ]);
    }

    public function update(PurchaseOrder $purchaseOrder, PurchaseOrderData $data, UpdatePurchaseOrderAction $update): RedirectResponse
    {
        $update->execute($purchaseOrder, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $purchaseOrder->po_number])]);

        return to_route('admin.purchase-orders.index');
    }

    /** Receive. */
    public function receive(PurchaseOrder $purchaseOrder, ReceivePurchaseOrderAction $action): RedirectResponse
    {
        $action->execute($purchaseOrder);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':number received — stock updated.', ['number' => $purchaseOrder->po_number])]);

        return back();
    }

    public function destroy(PurchaseOrder $purchaseOrder, DeletePurchaseOrderAction $delete): RedirectResponse
    {
        $delete->execute($purchaseOrder);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $purchaseOrder->po_number])]);

        return back();
    }
}
