<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Domains\Sales\Actions\ChangeStatusOrderAction;
use App\Domains\Sales\Actions\UpdateOrderAction;
use App\Domains\Sales\Data\OrderData;
use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Models\Order;
use App\Domains\Sales\Queries\OrderQueries;
use App\Domains\Sales\Queries\OrderSummaryQuery;
use App\Domains\Sales\Resources\OrderResource;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class OrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.OrderPermission::OrdersView->value, only: ['index']),
            new Middleware('can:'.OrderPermission::OrdersEdit->value, only: ['edit', 'update', 'changeStatus']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, OrderQueries $queries, OrderSummaryQuery $summary): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'orders', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (Order $order) => OrderResource::fromModel($order));

        return Inertia::render('order/index', [
            'records' => OrderResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
            'summary' => Inertia::defer(fn (): array => $summary->execute()),
        ]);
    }

    #[FormRules(OrderData::class)]
    public function edit(Order $order, OrderQueries $queries): Response
    {
        return Inertia::render('order/edit', [
            'record' => OrderResource::fromModel($order->load(['items', 'statusHistories.changedBy'])),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(Order $order, OrderData $data, UpdateOrderAction $update): RedirectResponse
    {
        $update->execute($order, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $order->order_number])]);

        return to_route('admin.orders.index');
    }

    /** Change status. */
    public function changeStatus(Request $request, Order $order, ChangeStatusOrderAction $action): RedirectResponse
    {
        $value = $request->validate(['status' => ['required', 'in:pending,confirmed,preparing,packaging,out_for_delivery,ready_for_pickup,completed,cancelled,refunded,archived']])['status'];
        $action->execute($order, $value);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':number is now :status.', ['number' => $order->order_number, 'status' => str_replace('_', ' ', $value)])]);

        return back();
    }
}
