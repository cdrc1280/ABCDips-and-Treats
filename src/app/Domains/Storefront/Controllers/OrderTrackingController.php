<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Sales\Models\Order;
use App\Domains\Setting\Models\Setting;
use App\Domains\Storefront\Resources\OrderPresenter;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public order look-up. An order is only shown for its unguessable tracking token, or for the
 * order number together with the email it was placed with — never by id or number alone.
 */
final class OrderTrackingController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('storefront/track', ['order' => null]);
    }

    public function lookup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $order = Order::query()
            ->where('order_number', trim($validated['order_number']))
            ->whereRaw('LOWER(customer_email) = ?', [mb_strtolower(trim($validated['email']))])
            ->first();

        if (! $order) {
            return back()->withErrors(['order_number' => __('We could not find an order with that number and email.')]);
        }

        return redirect()->route('orders.track', $order->tracking_token);
    }

    public function show(string $token): Response
    {
        return Inertia::render('storefront/track', ['order' => OrderPresenter::detail($this->byToken($token))]);
    }

    public function confirmation(string $token): Response
    {
        return Inertia::render('storefront/order-confirmation', [
            'order' => OrderPresenter::detail($this->byToken($token)),
            'bank' => [
                'name' => Setting::get('bank_name', 'BDO'),
                'account_name' => Setting::get('bank_account_name', 'ABCDips & Treats'),
                'account_number' => Setting::get('bank_account_number', ''),
                'instructions' => Setting::get('bank_instructions', ''),
            ],
        ]);
    }

    private function byToken(string $token): Order
    {
        abort_if(strlen($token) < 16, 404);

        return Order::query()->where('tracking_token', $token)->firstOrFail();
    }
}
