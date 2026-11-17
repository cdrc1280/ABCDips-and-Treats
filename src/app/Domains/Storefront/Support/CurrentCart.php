<?php

namespace App\Domains\Storefront\Support;

use App\Domains\Sales\Models\Cart;
use App\Domains\User\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Resolves the visitor's cart: the signed-in user's latest cart, else the guest cart whose
 * token lives in the session. Reading never creates rows; the first write does.
 */
final class CurrentCart
{
    public const SESSION_KEY = 'cart_token';

    public function __construct(private readonly CartService $carts) {}

    public function find(Request $request): ?Cart
    {
        $user = $request->user();

        $cart = $user ? Cart::query()->where('user_id', $user->id)->latest('updated_at')->first() : null;

        if (! $cart && ($token = $request->session()->get(self::SESSION_KEY))) {
            $cart = Cart::query()->where('token', $token)->whereNull('user_id')->first();
        }

        return $cart?->load(['items' => fn ($q) => $q->whereNull('deleted_at'), 'items.product']);
    }

    public function resolve(Request $request): Cart
    {
        $cart = $this->find($request);
        $user = $request->user();

        if (! $cart) {
            $cart = Cart::create([
                'token' => Str::random(32),
                'user_id' => $user?->id,
                'last_active_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);
        } else {
            $cart->update([
                'user_id' => $cart->user_id ?? $user?->id,
                'last_active_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);
        }

        if (! $user) {
            $request->session()->put(self::SESSION_KEY, $cart->token);
        }

        return $cart;
    }

    /** Moves a guest cart's items into the user's cart after sign-in. */
    public function mergeGuestCart(Request $request, User $user): void
    {
        $token = $request->session()->pull(self::SESSION_KEY);
        if (! $token) {
            return;
        }

        $guest = Cart::query()->where('token', $token)->whereNull('user_id')->with('items')->first();
        if (! $guest) {
            return;
        }

        $userCart = Cart::query()->where('user_id', $user->id)->latest('updated_at')->first();

        if (! $userCart) {
            $guest->update(['user_id' => $user->id]);

            return;
        }

        $this->carts->mergeCarts($guest, $userCart);
    }

    /**
     * @return array<string, mixed>
     */
    public static function present(?Cart $cart): array
    {
        if (! $cart) {
            return ['items' => [], 'item_count' => 0, 'subtotal' => 0.0, 'discount' => 0.0, 'fees' => 0.0, 'total' => 0.0, 'coupon_code' => null];
        }

        return [
            'items' => $cart->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => ! empty($item->options['is_custom']) && ! empty($item->options['custom_title'])
                    ? $item->options['custom_title']
                    : $item->product?->name,
                'slug' => $item->product?->slug,
                'image_url' => $item->product?->primary_image_url,
                'qty' => (int) $item->qty,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => $item->subtotal,
                'options' => $item->options,
            ])->values()->all(),
            'item_count' => (int) $cart->items->sum('qty'),
            'subtotal' => $cart->subtotal,
            'discount' => (float) $cart->discount_amount,
            'fees' => (float) $cart->fee_amount,
            'total' => $cart->total,
            'coupon_code' => $cart->coupon_code,
        ];
    }
}
