import { Head, Link } from '@inertiajs/react';
import { ShieldCheck, ShoppingBag, Trash2, Truck } from 'lucide-react';
import { useState } from 'react';

import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { CartLineRow, CartTotals, CouponBox } from '@/components/storefront/cart-parts';
import { EmptyState, PageHeader } from '@/components/storefront/page-header';
import { useStorefrontUi } from '@/components/storefront/storefront-context';
import { Button } from '@/components/ui/button';
import { useCart } from '@/hooks/use-cart';
import { money } from '@/lib/format';
import type { CartLine } from '@/types';

export default function CartPage() {
    const { cart, remove, removeMany } = useCart();
    const { openQuickView } = useStorefrontUi();
    const [selected, setSelected] = useState<string[]>([]);
    const [confirm, setConfirm] = useState<{ line?: CartLine; bulk?: boolean } | null>(null);

    const liveSelection = selected.filter((id) => cart.items.some((line) => line.id === id));
    const allSelected = cart.items.length > 0 && liveSelection.length === cart.items.length;

    return (
        <>
            <Head title="Your Basket" />
            <div className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <PageHeader title="Your basket" subtitle="Check quantities and add a promo code before checkout." />

                {cart.items.length === 0 ? (
                    <EmptyState
                        icon={<ShoppingBag className="size-7" />}
                        title="Your basket is empty"
                        description="Add a loaf or a box of brownies and it shows up here."
                        action={
                            <Button asChild size="lg">
                                <Link href="/shop">See the menu</Link>
                            </Button>
                        }
                    />
                ) : (
                    <div className="grid gap-8 lg:grid-cols-12">
                        <div className="space-y-3 lg:col-span-8">
                            <div className="flex items-center justify-between rounded-xl border border-primary/20 bg-card px-4 py-2.5 text-sm">
                                <label className="flex cursor-pointer items-center gap-2 font-bold text-foreground select-none">
                                    <input
                                        type="checkbox"
                                        checked={allSelected}
                                        onChange={() => setSelected(allSelected ? [] : cart.items.map((line) => line.id))}
                                        className="size-4 accent-primary"
                                    />
                                    Select all ({cart.items.length})
                                </label>
                                {liveSelection.length > 0 && (
                                    <Button size="sm" variant="destructive" onClick={() => setConfirm({ bulk: true })}>
                                        <Trash2 className="size-3.5" /> Delete ({liveSelection.length})
                                    </Button>
                                )}
                            </div>
                            {cart.items.map((line) => (
                                <CartLineRow
                                    key={line.id}
                                    line={line}
                                    large
                                    selected={liveSelection.includes(line.id)}
                                    onToggle={() =>
                                        setSelected((ids) => (ids.includes(line.id) ? ids.filter((x) => x !== line.id) : [...ids, line.id]))
                                    }
                                    onEdit={() => line.slug && openQuickView(line.slug, line)}
                                    onRemove={() => setConfirm({ line })}
                                />
                            ))}
                            <Link href="/shop" className="inline-block pt-2 text-sm font-bold text-primary hover:underline">
                                ← Continue shopping
                            </Link>
                        </div>

                        <aside className="lg:col-span-4">
                            <div className="receipt-edge sticky top-24 space-y-5 rounded-t-2xl bg-card p-6 shadow-md">
                                <div className="border-b border-dashed border-border pb-4 text-center font-mono">
                                    <h2 className="text-sm font-semibold tracking-widest text-foreground uppercase">Order summary</h2>
                                    <p className="mt-0.5 text-xs text-muted-foreground">
                                        {cart.item_count} {cart.item_count === 1 ? 'item' : 'items'}
                                    </p>
                                </div>
                                <CouponBox />
                                <CartTotals deliveryFee={null} />
                                <Button asChild size="lg" className="w-full">
                                    <Link href="/checkout">Checkout • {money(cart.total)}</Link>
                                </Button>
                                <ul className="space-y-2 text-xs text-muted-foreground">
                                    <li className="flex items-center gap-2">
                                        <Truck className="size-4 text-primary" /> Lalamove delivery or free store pickup
                                    </li>
                                    <li className="flex items-center gap-2">
                                        <ShieldCheck className="size-4 text-primary" /> GCash, Maya, bank transfer or cash on delivery
                                    </li>
                                </ul>
                            </div>
                        </aside>
                    </div>
                )}
            </div>

            <ConfirmDialog
                open={confirm !== null}
                onOpenChange={(open) => !open && setConfirm(null)}
                title={confirm?.bulk ? `Remove ${liveSelection.length} items?` : 'Remove this treat?'}
                description={
                    confirm?.bulk
                        ? 'The selected items will be removed from your basket.'
                        : `“${confirm?.line?.name}” will be removed. You can undo right after.`
                }
                confirmLabel="Remove"
                onConfirm={() => {
                    if (confirm?.bulk) {
                        removeMany(liveSelection);
                        setSelected([]);
                    } else if (confirm?.line) {
                        remove(confirm.line.id);
                    }
                    setConfirm(null);
                }}
            />
        </>
    );
}
