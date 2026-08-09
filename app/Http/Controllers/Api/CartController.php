<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    private function getCart(Request $request)
    {
        $token = $request->header('X-Cart-Token') ?: $request->get('cart_token');
        return $this->cartService->getOrCreateCart($token, $request->user('sanctum'));
    }

    public function show(Request $request): JsonResponse
    {
        $cart = $this->getCart($request);
        return response()->json(['data' => new CartResource($cart)]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['required', 'integer', 'min:1'],
            'options'    => ['nullable', 'array'],
        ]);

        $cart = $this->getCart($request);
        $this->cartService->addItem($cart, (int)$request->product_id, (int)$request->qty, $request->options);

        return response()->json([
            'message' => 'Item added to cart.',
            'data'    => new CartResource($cart->fresh(['items.product'])),
        ]);
    }

    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $request->validate([
            'qty' => ['required', 'integer', 'min:0'],
            'options' => ['nullable', 'array'],
        ]);

        $cart = $this->getCart($request);
        $this->cartService->updateItem($cart, $itemId, (int)$request->qty, $request->options);

        return response()->json([
            'message' => 'Cart updated.',
            'data'    => new CartResource($cart->fresh(['items.product'])),
        ]);
    }

    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->getCart($request);
        $removed = $this->cartService->removeItem($cart, $itemId);

        return response()->json([
            'message' => $removed ? 'Item removed from cart.' : 'Item not found.',
            'data'    => new CartResource($cart->fresh(['items.product'])),
        ]);
    }

    public function restoreItem(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->getCart($request);
        $restored = $this->cartService->restoreItem($cart, $itemId);

        return response()->json([
            'message' => $restored ? 'Item restored to cart.' : 'Could not restore item.',
            'data'    => new CartResource($cart->fresh(['items.product'])),
        ]);
    }

    public function batch(Request $request): JsonResponse
    {
        $request->validate([
            'operations'   => ['required', 'array'],
            'operations.*' => ['required', 'array'],
        ]);

        $cart = $this->getCart($request);
        $this->cartService->batchUpdate($cart, $request->operations);

        return response()->json([
            'message' => 'Batch cart update applied.',
            'data'    => new CartResource($cart->fresh(['items.product'])),
        ]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $cart = $this->getCart($request);
        $result = $this->cartService->applyCoupon($cart, strtoupper(trim($request->code)));

        if (! $result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'data'    => new CartResource($cart->fresh(['items.product'])),
        ]);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $cart = $this->getCart($request);
        $this->cartService->removeCoupon($cart);

        return response()->json([
            'message' => 'Coupon removed.',
            'data'    => new CartResource($cart->fresh(['items.product'])),
        ]);
    }
}
