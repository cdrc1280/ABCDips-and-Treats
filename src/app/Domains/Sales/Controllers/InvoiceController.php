<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Sales\Enums\OrderPermission;
use App\Domains\Sales\Models\Order;
use App\Domains\Setting\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Order invoice (A4) and 80mm POS receipt as one React print page (`storefront/invoice`).
 * "Download PDF" opens the browser print dialog (Save as PDF, or print to the thermal printer).
 * Open to staff with orders-view, the order's owner, or anyone holding its tracking token.
 */
class InvoiceController extends Controller
{
    public function show(Request $request, Order $order): Response
    {
        $this->authorizeInvoice($request, $order);
        $order->loadMissing(['items.product', 'deliveryPool']);

        return Inertia::render('storefront/invoice', [
            'invoice' => $this->present($order),
            'paper' => in_array(strtolower((string) $request->query('paper', 'a4')), ['pos', '80mm', 'receipt'], true) ? 'pos' : 'a4',
            'autoPrint' => $request->boolean('print'),
        ]);
    }

    /** Old "download" links (emails, bookmarks) open the same page with the print dialog. */
    public function download(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeInvoice($request, $order);

        return redirect()->route('order.invoice', array_filter([
            'order' => $order->id,
            'token' => $request->query('token'),
            'paper' => $request->query('paper'),
            'print' => 1,
        ]));
    }

    private function authorizeInvoice(Request $request, Order $order): void
    {
        $user = $request->user();
        $isStaff = $user?->can(OrderPermission::OrdersView->value) ?? false;
        $token = (string) $request->query('token', '');
        $hasToken = $token !== '' && $order->tracking_token && hash_equals($order->tracking_token, $token);

        if (! $isStaff && ! $hasToken && ! ($user && $order->user_id === $user->id)) {
            abort($user ? 403 : 401);
        }

        // Group-pooling invoices stay locked for customers until the pool is settled.
        if (! $isStaff && $order->delivery_mode === Order::MODE_POOLING && $order->pooling_status !== Order::POOLING_SETTLED) {
            abort(403, __('The invoice is available once your group delivery is settled.'));
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Order $order): array
    {
        return [
            'order_number' => $order->order_number,
            'issued_at' => $order->created_at?->toIso8601String(),
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'address' => trim(implode(', ', array_filter([$order->delivery_address, $order->city, $order->postal_code]))),
            'fulfillment' => (string) $order->fulfillment_type,
            'pool_code' => $order->delivery_mode === Order::MODE_POOLING ? ($order->deliveryPool->pool_code ?? 'POOL') : null,
            'payment_method' => (string) $order->payment_method,
            'payment_status' => (string) $order->payment_status,
            'payment_reference' => $order->payment_reference,
            'subtotal' => (float) $order->subtotal,
            'discount' => (float) $order->discount_amount,
            'delivery_fee' => (float) $order->delivery_fee,
            'total' => (float) $order->total,
            'bank' => ['name' => Setting::get('bdo_account_name', ''), 'number' => Setting::get('bdo_account_number', '')],
            'items' => $order->items->map(function ($item) {
                $options = (array) $item->options;

                return [
                    'name' => $item->product_name ?? $item->product->name ?? 'Item',
                    'detail' => implode(' · ', array_filter([
                        ! empty($options['flavors']) && is_array($options['flavors']) ? 'Assorted: '.implode(', ', $options['flavors']) : ($options['flavor'] ?? null),
                        $options['variation'] ?? null,
                    ])) ?: null,
                    'qty' => (int) ($item->qty ?? 1),
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal,
                ];
            })->all(),
        ];
    }
}
