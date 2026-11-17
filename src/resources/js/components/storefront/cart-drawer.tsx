import { Link } from '@inertiajs/react';
import { ShoppingBag, Trash2 } from 'lucide-react';
import { useState } from 'react';

import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { CartLineRow, CartTotals, CouponBox } from '@/components/storefront/cart-parts';
import { useStorefrontUi } from '@/components/storefront/storefront-context';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetDescription, SheetTitle } from '@/components/ui/sheet';
import { useCart } from '@/hooks/use-cart';
import { money } from '@/lib/format';
import type { CartLine } from '@/types';

/** Slide-over basket: quantities, bulk delete, coupon, totals, checkout. */
export default function CartDrawer() {
    const { cartOpen, setCartOpen, openQuickView } = useStorefrontUi();
    const { cart, remove, removeMany } = useCart();
    const [selected, setSelected] = useState<string[]>([]);
    const [confirm, setConfirm] = useState<{ line?: CartLine; bulk?: boolean } | null>(null);

    // Lines removed elsewhere drop out of the selection.
    const liveSelection = selected.filter((id) => cart.items.some((line) => line.id === id));
    const allSelected = cart.items.length > 0 && liveSelection.length === cart.items.length;

    const toggle = (id: string) => setSelected((ids) => (ids.includes(id) ? ids.filter((x) => x !== id) : [...ids, id]));

    const confirmRemove = () => {
        if (confirm?.bulk) {
            removeMany(liveSelection);
            setSelected([]);
        } else if (confirm?.line) {
            remove(confirm.line.id);
        }
        setConfirm(null);
    };

    return (
        <>
            <Sheet open={cartOpen} onOpenChange={setCartOpen}>
                <SheetContent className="w-full gap-0 p-0 sm:max-w-md">
                    <div className="flex items-center gap-3 border-b bg-background p-5 pr-12 sm:p-6">
                        <div className="flex size-9 items-center justify-center rounded-lg bg-highlight/25 text-primary">
                            <ShoppingBag className="size-4" />
                        </div>
                        <div>
                            <SheetTitle className="font-display text-lg font-normal tracking-tight sm:text-xl">Your basket</SheetTitle>
                            <SheetDescription className="text-xs">
                                {cart.item_count} {cart.item_count === 1 ? 'item' : 'items'} selected
                            </SheetDescription>
                        </div>
                    </div>

                    {cart.items.length > 0 && (
                        <div className="flex items-center justify-between border-b bg-muted/50 px-6 py-2.5 text-xs">
                            <label className="flex cursor-pointer items-center gap-2 font-medium select-none">
                                <input
                                    type="checkbox"
                                    checked={allSelected}
                                    onChange={() => setSelected(allSelected ? [] : cart.items.map((line) => line.id))}
                                    className="size-3.5 cursor-pointer rounded accent-primary"
                                />
                                Select All
                            </label>
                            {liveSelection.length > 0 && (
                                <button
                                    type="button"
                                    onClick={() => setConfirm({ bulk: true })}
                                    className="flex items-center gap-1 rounded-md bg-destructive/10 px-2.5 py-1 font-medium text-destructive-ink transition-colors hover:bg-destructive/15"
                                >
                                    <Trash2 className="size-3" />
                                    Delete ({liveSelection.length})
                                </button>
                            )}
                        </div>
                    )}

                    <div className="flex-1 overflow-y-auto px-5 py-4 sm:px-6">
                        {cart.items.length === 0 ? (
                            <div className="flex flex-col items-center py-16 text-center">
                                <div className="mb-4 flex size-14 items-center justify-center rounded-full bg-highlight/25 text-primary">
                                    <ShoppingBag className="size-7" />
                                </div>
                                <h3 className="font-display text-xl">Your Basket is Empty</h3>
                                <p className="mt-1 max-w-xs text-sm text-muted-foreground">
                                    Nothing here yet. Add a loaf or a box of brownies from the menu.
                                </p>
                                <Button asChild className="mt-6">
                                    <Link href="/shop" onClick={() => setCartOpen(false)}>
                                        Browse Bakery Menu
                                    </Link>
                                </Button>
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {cart.items.map((line) => (
                                    <CartLineRow
                                        key={line.id}
                                        line={line}
                                        selected={liveSelection.includes(line.id)}
                                        onToggle={() => toggle(line.id)}
                                        onEdit={() => {
                                            setCartOpen(false);
                                            if (line.slug) openQuickView(line.slug, line);
                                        }}
                                        onRemove={() => setConfirm({ line })}
                                    />
                                ))}
                            </div>
                        )}
                    </div>

                    {cart.items.length > 0 && (
                        <div className="space-y-4 border-t bg-background p-5 sm:p-6">
                            <CouponBox />

                            <CartTotals />

                            <div className="space-y-2 pt-1">
                                <Button asChild size="lg" className="w-full">
                                    <Link href="/checkout" onClick={() => setCartOpen(false)}>
                                        Checkout • {money(cart.total)}
                                    </Link>
                                </Button>
                                <Button asChild size="sm" variant="outline" className="w-full">
                                    <Link href="/cart" onClick={() => setCartOpen(false)}>
                                        View Full Cart Page
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    )}
                </SheetContent>
            </Sheet>

            <ConfirmDialog
                open={confirm !== null}
                onOpenChange={(open) => !open && setConfirm(null)}
                title={confirm?.bulk ? `Remove ${liveSelection.length} items?` : 'Remove this treat?'}
                description={
                    confirm?.bulk
                        ? 'The selected items will be removed from your basket.'
                        : `“${confirm?.line?.name}” will be removed from your basket. You can undo right after.`
                }
                confirmLabel="Remove"
                onConfirm={confirmRemove}
            />
        </>
    );
}
