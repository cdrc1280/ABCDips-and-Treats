<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Storefront\Data\CartItemData;
use App\Domains\Storefront\Data\CartItemUpdateData;
use App\Domains\Storefront\Support\CurrentCart;
use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CartController extends Controller
{
    public function __construct(
        private readonly CurrentCart $current,
        private readonly CartService $carts,
    ) {}

    public function show(Request $request): Response
    {
        if ($cart = $this->current->find($request)) {
            $this->carts->recalculateCartTotals($cart);
        }

        return Inertia::render('storefront/cart');
    }

    public function store(Request $request, CartItemData $data): RedirectResponse
    {
        $cart = $this->current->resolve($request);
        $this->carts->addItem($cart, $data->productId, $data->qty, $data->options);

        return $this->done(__('Added to your basket.'), openDrawer: true);
    }

    public function update(Request $request, string $item, CartItemUpdateData $data): RedirectResponse
    {
        $cart = $this->current->resolve($request);
        abort_if($this->carts->updateItem($cart, $item, $data->qty, $data->options) === null, 404);

        return back();
    }

    public function destroy(Request $request, string $item): RedirectResponse
    {
        $cart = $this->current->resolve($request);
        abort_unless($this->carts->removeItem($cart, $item), 404);

        return $this->done(__('Item removed.'), undo: $item);
    }

    public function restore(Request $request, string $item): RedirectResponse
    {
        $cart = $this->current->resolve($request);
        abort_if($this->carts->restoreItem($cart, $item) === null, 404);

        return $this->done(__('Item restored.'));
    }

    public function batch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'operations' => ['required', 'array', 'max:100'],
            'operations.*.type' => ['required', 'in:update,remove,restore'],
            'operations.*.item_id' => ['required', 'uuid'],
            'operations.*.qty' => ['nullable', 'integer', 'min:0', 'max:99'],
            'operations.*.options' => ['nullable', 'array'],
        ]);

        $cart = $this->current->resolve($request);
        $this->carts->batchUpdate($cart, $validated['operations']);

        return $this->done(__('Basket updated.'));
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $code = strtoupper(trim((string) $request->validate(['code' => ['required', 'string', 'max:50']])['code']));
        $result = $this->carts->applyCoupon($this->current->resolve($request), $code);

        if (! $result['success']) {
            return back()->withErrors(['code' => $result['message']]);
        }

        return $this->done($result['message']);
    }

    public function removeCoupon(Request $request): RedirectResponse
    {
        if ($cart = $this->current->find($request)) {
            $this->carts->removeCoupon($cart);
        }

        return $this->done(__('Coupon removed.'));
    }

    private function done(string $message, bool $openDrawer = false, ?string $undo = null): RedirectResponse
    {
        Inertia::flash('toast', ['type' => 'success', 'message' => $message]);
        if ($openDrawer) {
            Inertia::flash('cart_drawer', true);
        }
        if ($undo) {
            Inertia::flash('cart_undo', $undo);
        }

        return back();
    }
}
