import { Minus, Plus, Ticket } from 'lucide-react';
import { useState, type ReactNode } from 'react';

import { CartLineDetails } from '@/components/storefront/cart-line-details';
import { Button } from '@/components/ui/button';
import { useCart } from '@/hooks/use-cart';
import { money } from '@/lib/format';
import type { CartLine } from '@/types';

export const PLACEHOLDER_IMAGE = '/images/logo.png';

function QtyButton({ label, onClick, disabled, children }: { label: string; onClick: () => void; disabled?: boolean; children: ReactNode }) {
    return (
        <button
            type="button"
            aria-label={label}
            title={label}
            onClick={onClick}
            disabled={disabled}
            className="flex size-7 items-center justify-center rounded-lg border border-primary/20 bg-background text-primary transition hover:bg-highlight/30 disabled:opacity-40"
        >
            {children}
        </button>
    );
}

/** One basket line: select box, photo, options, qty stepper, subtotal, edit/remove. */
export function CartLineRow({
    line,
    selected,
    onToggle,
    onEdit,
    onRemove,
    large = false,
}: {
    line: CartLine;
    selected: boolean;
    onToggle: () => void;
    onEdit?: () => void;
    onRemove: () => void;
    large?: boolean;
}) {
    const { update } = useCart();

    return (
        <div className="flex animate-in items-start gap-3 rounded-2xl border border-primary/15 bg-background/40 p-3.5 transition-all fade-in hover:border-primary/30">
            <input
                type="checkbox"
                checked={selected}
                onChange={onToggle}
                aria-label={`Select ${line.name}`}
                className="mt-2 size-3.5 shrink-0 cursor-pointer rounded accent-primary"
            />
            <img
                src={line.image_url || PLACEHOLDER_IMAGE}
                alt=""
                className={`shrink-0 rounded-xl border border-primary/20 bg-background/60 object-cover ${large ? 'size-20' : 'size-16'}`}
            />
            <div className="min-w-0 flex-1">
                <h4 className={`truncate font-bold text-foreground ${large ? 'text-base' : 'text-sm'}`}>{line.name}</h4>
                <CartLineDetails line={line} />
                <div className="mt-1 font-mono text-xs text-muted-foreground tabular-nums">{money(line.unit_price)} each</div>
                <div className="mt-2 flex items-center gap-1.5">
                    <QtyButton label="Decrease quantity" onClick={() => update(line.id, line.qty - 1)}>
                        <Minus className="size-3" />
                    </QtyButton>
                    <span className="w-7 text-center font-mono text-sm font-bold text-foreground tabular-nums">{line.qty}</span>
                    <QtyButton label="Increase quantity" onClick={() => update(line.id, line.qty + 1)} disabled={line.qty >= 99}>
                        <Plus className="size-3" />
                    </QtyButton>
                </div>
            </div>
            <div className="shrink-0 text-right">
                <div className="font-mono text-sm font-semibold text-primary tabular-nums">{money(line.subtotal)}</div>
                {onEdit && !line.options?.is_custom && line.slug && (
                    <button type="button" className="mt-1 ml-auto block text-xs font-semibold text-primary hover:underline" onClick={onEdit}>
                        Edit
                    </button>
                )}
                <button type="button" className="mt-1 ml-auto block text-xs text-destructive hover:underline" onClick={onRemove}>
                    Remove
                </button>
            </div>
        </div>
    );
}

export function CouponBox() {
    const { cart, applyCoupon, removeCoupon } = useCart();
    const [code, setCode] = useState('');
    const [applying, setApplying] = useState(false);

    if (cart.coupon_code) {
        return (
            <div className="flex items-center justify-between rounded-xl border border-success/30 bg-success/15 p-2.5 text-xs">
                <span className="flex items-center gap-1.5 font-bold tracking-wide uppercase">
                    <Ticket className="size-4 text-primary" />
                    {cart.coupon_code}
                </span>
                <button type="button" className="font-bold text-destructive hover:underline" onClick={removeCoupon}>
                    Remove
                </button>
            </div>
        );
    }

    return (
        <form
            className="flex gap-2"
            onSubmit={(event) => {
                event.preventDefault();
                if (!code.trim()) return;
                setApplying(true);
                applyCoupon(code, () => {
                    setApplying(false);
                    setCode('');
                });
            }}
        >
            <input
                value={code}
                onChange={(event) => setCode(event.target.value)}
                placeholder="PROMO CODE"
                aria-label="Promo code"
                className="flex-1 rounded-xl border border-primary/30 bg-card px-3 py-2 text-xs font-bold tracking-wider text-foreground uppercase focus:ring-2 focus:ring-primary focus:outline-none"
            />
            <Button type="submit" size="sm" variant="secondary" disabled={applying}>
                Apply
            </Button>
        </form>
    );
}

export function CartTotals({ deliveryFee, label = 'Estimated Total' }: { deliveryFee?: number | null; label?: string }) {
    const { cart } = useCart();
    const total = cart.total + (deliveryFee ?? 0);

    return (
        <div className="space-y-1.5 text-sm text-muted-foreground">
            <div className="flex justify-between">
                <span>Subtotal</span>
                <span className="font-mono font-bold text-foreground tabular-nums">{money(cart.subtotal)}</span>
            </div>
            {cart.discount > 0 && (
                <div className="flex justify-between text-success">
                    <span>Voucher Discount</span>
                    <span className="font-mono font-bold tabular-nums">-{money(cart.discount)}</span>
                </div>
            )}
            {cart.fees > 0 && (
                <div className="flex justify-between">
                    <span>Handling Fee</span>
                    <span className="font-mono font-bold text-foreground tabular-nums">{money(cart.fees)}</span>
                </div>
            )}
            {deliveryFee !== undefined && (
                <div className="flex justify-between">
                    <span>Delivery</span>
                    <span className="font-mono font-bold text-foreground tabular-nums">
                        {deliveryFee === null ? 'Calculated at checkout' : deliveryFee === 0 ? 'Free' : money(deliveryFee)}
                    </span>
                </div>
            )}
            <div className="flex justify-between border-t border-primary/20 pt-2 text-base font-semibold text-primary">
                <span>{label}</span>
                <span className="font-mono tabular-nums">{money(total)}</span>
            </div>
        </div>
    );
}
