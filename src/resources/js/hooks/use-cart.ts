import { router, usePage } from '@inertiajs/react';
import { useCallback } from 'react';

import cartRoutes from '@/routes/cart';
import type { CartLineOptions, CartSummary } from '@/types';

const EMPTY: CartSummary = { items: [], item_count: 0, subtotal: 0, discount: 0, fees: 0, total: 0, coupon_code: null };
const quiet = { preserveScroll: true, preserveState: true } as const;

/** Shared `cart` prop plus the cart write actions (Inertia visits that redirect back with a fresh prop). */
export function useCart() {
    const cart = usePage().props.cart ?? EMPTY;

    const add = useCallback(
        (productId: string, qty = 1, options: CartLineOptions | null = null, onSuccess?: () => void) =>
            router.post(
                cartRoutes.items.store().url,
                { product_id: productId, qty, options: options as Record<string, never> | null },
                { ...quiet, onSuccess },
            ),
        [],
    );
    const update = useCallback(
        (itemId: string, qty: number, options?: CartLineOptions) =>
            router.patch(cartRoutes.items.update(itemId).url, { qty, ...(options ? { options: options as Record<string, never> } : {}) }, quiet),
        [],
    );
    const remove = useCallback((itemId: string) => router.delete(cartRoutes.items.destroy(itemId).url, quiet), []);
    const restore = useCallback((itemId: string) => router.post(cartRoutes.items.restore(itemId).url, {}, quiet), []);
    const removeMany = useCallback(
        (ids: string[]) => router.post(cartRoutes.batch().url, { operations: ids.map((id) => ({ type: 'remove', item_id: id })) }, quiet),
        [],
    );
    const applyCoupon = useCallback(
        (code: string, onFinish?: () => void) => router.post(cartRoutes.coupon.apply().url, { code }, { ...quiet, onFinish }),
        [],
    );
    const removeCoupon = useCallback(() => router.delete(cartRoutes.coupon.remove().url, quiet), []);

    return { cart, add, update, remove, restore, removeMany, applyCoupon, removeCoupon };
}
