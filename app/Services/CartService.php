<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class CartService
{
    public function getOrCreateCart(?string $token = null, ?User $user = null): Cart
    {
        $cart = null;

        // 1. If user is authenticated, prioritize finding their active user cart in DB
        if ($user) {
            $cart = Cart::where('user_id', $user->id)
                ->latest('updated_at')
                ->first();
        }

        // 2. If no user cart found yet, search by token
        if (! $cart && $token) {
            $cart = Cart::where('token', $token)
                ->first();
        }

        // 3. Create new cart if none exists
        if (! $cart) {
            $cart = Cart::create([
                'token'          => Str::random(32),
                'user_id'        => $user?->id,
                'last_active_at' => now(),
                'expires_at'     => now()->addDays(30),
            ]);
        } else {
            // Update user linkage & expiration
            if ($user && ! $cart->user_id) {
                $cart->update(['user_id' => $user->id]);
            }
            $cart->update([
                'last_active_at' => now(),
                'expires_at'     => now()->addDays(30),
            ]);
        }

        $this->recalculateCartTotals($cart);

        return $cart->load(['items.product', 'items' => fn ($q) => $q->whereNull('deleted_at')]);
    }

    public function addItem(Cart $cart, int $productId, int $qty = 1, ?array $options = null): CartItem
    {
        $product = Product::findOrFail($productId);

        // Respect custom bake price set in options if present
        $unitPrice = (isset($options['unit_price']) && (float) $options['unit_price'] > 0)
            ? (float) $options['unit_price']
            : $product->effective_price;

        $isCustom = !empty($options['is_custom']);

        $query = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->whereNull('deleted_at');

        if ($isCustom) {
            $query->where('options', json_encode($options));
        }

        $existingItem = $query->first();

        if ($existingItem) {
            $existingItem->update([
                'qty'        => $existingItem->qty + $qty,
                'unit_price' => $unitPrice,
                'options'    => $options ?? $existingItem->options,
            ]);
            $item = $existingItem;
        } else {
            $item = CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $productId,
                'qty'        => $qty,
                'unit_price' => $unitPrice,
                'options'    => $options,
            ]);
        }

        $this->recalculateCartTotals($cart);

        return $item;
    }

    public function updateItem(Cart $cart, int $itemId, int $qty): ?CartItem
    {
        $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();

        if (! $item) {
            return null;
        }

        if ($qty <= 0) {
            $item->delete(); // Soft delete for restore capability
        } else {
            $item->update(['qty' => $qty]);
        }

        $this->recalculateCartTotals($cart);

        return $item;
    }

    public function removeItem(Cart $cart, int $itemId): bool
    {
        $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();

        if ($item) {
            $item->delete(); // Soft delete for restore capability
            $this->recalculateCartTotals($cart);
            return true;
        }

        return false;
    }

    public function restoreItem(Cart $cart, int $itemId): ?CartItem
    {
        $item = CartItem::onlyTrashed()
            ->where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->first();

        if ($item) {
            $item->restore();
            $this->recalculateCartTotals($cart);
            return $item;
        }

        return null;
    }

    public function batchUpdate(Cart $cart, array $operations): Cart
    {
        foreach ($operations as $op) {
            $type = $op['type'] ?? 'update'; // add, update, remove, restore
            if ($type === 'add' && isset($op['product_id'])) {
                $this->addItem($cart, (int)$op['product_id'], (int)($op['qty'] ?? 1), $op['options'] ?? null);
            } elseif ($type === 'update' && isset($op['item_id'])) {
                $this->updateItem($cart, (int)$op['item_id'], (int)$op['qty']);
            } elseif ($type === 'remove' && isset($op['item_id'])) {
                $this->removeItem($cart, (int)$op['item_id']);
            } elseif ($type === 'restore' && isset($op['item_id'])) {
                $this->restoreItem($cart, (int)$op['item_id']);
            }
        }

        return $this->recalculateCartTotals($cart);
    }

    public function applyCoupon(Cart $cart, string $code): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            return ['success' => false, 'message' => 'Invalid coupon code.'];
        }

        $subtotal = $cart->subtotal;

        if (! $coupon->isValid($subtotal)) {
            return ['success' => false, 'message' => 'Coupon requirements are not met.'];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        $cart->update([
            'coupon_code'     => $coupon->code,
            'discount_amount' => $discount,
        ]);

        return [
            'success'  => true,
            'message'  => 'Coupon applied successfully!',
            'discount' => $discount,
        ];
    }

    public function removeCoupon(Cart $cart): void
    {
        $cart->update([
            'coupon_code'     => null,
            'discount_amount' => 0,
        ]);

        $this->recalculateCartTotals($cart);
    }

    public function mergeCarts(Cart $guestCart, Cart $userCart): Cart
    {
        if ($guestCart->id === $userCart->id) {
            return $this->recalculateCartTotals($userCart);
        }

        foreach ($guestCart->items as $gItem) {
            $this->addItem($userCart, $gItem->product_id, $gItem->qty, $gItem->options);
        }

        if ($guestCart->coupon_code && ! $userCart->coupon_code) {
            $this->applyCoupon($userCart, $guestCart->coupon_code);
        }

        $guestCart->delete();

        return $this->recalculateCartTotals($userCart);
    }

    public function recalculateCartTotals(Cart $cart): Cart
    {
        $cart->load(['items' => fn ($q) => $q->whereNull('deleted_at'), 'items.product']);
        $subtotal = $cart->subtotal;

        if ($cart->coupon_code) {
            $coupon = Coupon::where('code', $cart->coupon_code)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $cart->discount_amount = $coupon->calculateDiscount($subtotal);
            } else {
                $cart->coupon_code = null;
                $cart->discount_amount = 0;
            }
        }

        $cart->save();

        return $cart;
    }
}
