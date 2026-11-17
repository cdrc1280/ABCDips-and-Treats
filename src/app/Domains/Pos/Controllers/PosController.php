<?php

namespace App\Domains\Pos\Controllers;

use App\Domains\Pos\Actions\CheckoutPosSaleAction;
use App\Domains\Pos\Data\PosCheckoutData;
use App\Domains\Pos\Enums\PosPermission;
use App\Domains\Pos\Queries\PosProductQueries;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:'.PosPermission::PosUse->value)];
    }

    public function index(PosProductQueries $products): Response
    {
        return Inertia::render('pos/index', ['products' => $products->sellable()]);
    }

    public function checkout(PosCheckoutData $data, CheckoutPosSaleAction $checkout): RedirectResponse
    {
        $result = $checkout->execute($data);
        $order = $result['order']->load('items');

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Sale :number completed.', ['number' => $order->order_number])]);
        Inertia::flash('receipt', [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'total' => (float) $order->total,
            'payment_method' => $order->payment_method,
            'cash_tendered' => $result['cash_tendered'],
            'change_due' => $result['change_due'],
            'items' => $order->items->map(fn ($i) => ['name' => $i->product_name, 'qty' => (int) $i->qty, 'subtotal' => (float) $i->subtotal])->all(),
        ]);

        return to_route('admin.pos.index');
    }
}
