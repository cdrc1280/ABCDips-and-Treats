import { Check, Plus } from 'lucide-react';
import { useState } from 'react';

import { CategoryIcon } from '@/components/storefront/category-icon';
import { useStorefrontUi } from '@/components/storefront/storefront-context';
import { useCart } from '@/hooks/use-cart';
import { useCountdown } from '@/hooks/use-countdown';
import { money } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { ProductCardData } from '@/types';

/**
 * The product's real photo, or a designed stand-in when it has none:
 * parchment with a dashed label edge, a category icon and a serif caption. Never the logo.
 * `caption` defaults to the product name; cards pass the category name since the title sits right below.
 */
export function ProductPhoto({
    src,
    name,
    categorySlug,
    caption,
    className,
    imgClassName,
    eager = false,
}: {
    src: string | null | undefined;
    name: string;
    categorySlug?: string | null;
    caption?: string | null;
    className?: string;
    imgClassName?: string;
    eager?: boolean;
}) {
    const [broken, setBroken] = useState(false);

    if (src && !broken) {
        return (
            <img
                src={src}
                alt={name}
                loading={eager ? 'eager' : 'lazy'}
                decoding="async"
                onError={() => setBroken(true)}
                className={cn('size-full object-cover object-center', imgClassName, className)}
            />
        );
    }

    return (
        <div
            role="img"
            aria-label={name}
            className={cn(
                'relative flex size-full flex-col items-center justify-center gap-3 overflow-hidden bg-parchment p-5 text-center text-parchment-foreground',
                className,
            )}
        >
            <span aria-hidden className="pointer-events-none absolute inset-2 rounded-xl border border-dashed border-current/25 sm:inset-3" />
            <span
                aria-hidden
                className="relative flex size-11 items-center justify-center rounded-full border border-dashed border-current/40 sm:size-14"
            >
                <CategoryIcon slug={categorySlug} className="size-5 sm:size-6" strokeWidth={1.75} />
            </span>
            <span className="relative line-clamp-3 font-display text-base leading-tight text-balance sm:text-lg">{caption || name}</span>
        </div>
    );
}

/** One status at most, in priority order. */
function cardStatus(product: ProductCardData, onSale: boolean): { label: string; tone: string } | null {
    if (!product.is_in_stock) return { label: 'Sold out', tone: 'bg-card text-muted-foreground' };
    if (onSale) return { label: 'Sale', tone: 'bg-destructive text-destructive-foreground' };
    if (product.is_best_seller) return { label: 'Best seller', tone: 'bg-primary text-primary-foreground' };
    if (product.is_new_arrival) return { label: 'New', tone: 'bg-success text-success-foreground' };
    return null;
}

/** Listing card styled as a bakery label: photo, name, price tag, one Add button. Tapping the card opens quick view. */
export function ProductCard({ product }: { product: ProductCardData }) {
    const { openQuickView } = useStorefrontUi();
    const { add } = useCart();
    const [added, setAdded] = useState(false);
    const countdown = useCountdown(product.sale_ends_at);
    const onSale = product.is_on_sale && !countdown?.expired;
    const status = cardStatus(product, onSale);
    const open = () => openQuickView(product.slug);

    // Simple products go straight into the basket; anything with choices opens quick view first.
    const quickAdd = !product.has_options && product.is_in_stock;
    const onAdd = (event: React.MouseEvent) => {
        event.stopPropagation();
        if (!quickAdd) return open();
        add(product.id, 1, null, () => {
            setAdded(true);
            window.setTimeout(() => setAdded(false), 1600);
        });
    };

    return (
        <article
            className="group flex cursor-pointer flex-col overflow-hidden rounded-2xl border border-border bg-card shadow-xs transition-shadow duration-300 hover:shadow-md"
            onClick={open}
        >
            <div className="relative aspect-square overflow-hidden">
                <ProductPhoto
                    src={product.image_url}
                    name={product.name}
                    categorySlug={product.category?.slug}
                    caption={product.category?.name}
                    imgClassName="transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                />
                {status && (
                    <span className={cn('absolute top-2 left-2 rounded-md px-2 py-0.5 text-[11px] font-semibold shadow-sm', status.tone)}>
                        {status.label}
                    </span>
                )}
            </div>

            <div className="flex flex-1 flex-col gap-3 p-3 sm:p-4">
                <h3 className="line-clamp-2 text-sm leading-snug font-semibold text-foreground sm:text-base">{product.name}</h3>

                <div className="mt-auto flex items-end justify-between gap-2">
                    {/* Price tag: kraft swing tag with a punched hole. */}
                    <span className="relative inline-flex min-w-0 items-baseline gap-1.5 rounded-l-sm rounded-r-md bg-kraft py-1 pr-2.5 pl-4 text-kraft-foreground shadow-xs">
                        <span aria-hidden className="absolute top-1/2 left-1.5 size-1.5 -translate-y-1/2 rounded-full bg-card" />
                        <span className="font-mono text-sm font-semibold tabular-nums sm:text-base">
                            {money(onSale ? product.effective_price : product.price)}
                        </span>
                        {onSale && (
                            <span className="truncate font-mono text-[11px] tabular-nums line-through opacity-70">{money(product.price)}</span>
                        )}
                    </span>

                    <button
                        type="button"
                        onClick={onAdd}
                        disabled={!product.is_in_stock}
                        aria-label={quickAdd ? `Add ${product.name} to basket` : `Choose options for ${product.name}`}
                        className="inline-flex h-9 shrink-0 items-center gap-1 rounded-xl bg-primary px-3 text-xs font-semibold text-primary-foreground transition-colors hover:bg-primary/90 disabled:bg-muted disabled:text-muted-foreground sm:text-sm"
                    >
                        {added ? <Check className="size-4" /> : <Plus className="size-4" />}
                        {added ? 'Added' : 'Add'}
                    </button>
                </div>
            </div>
        </article>
    );
}

export function ProductGrid({ products, className }: { products: ProductCardData[]; className?: string }) {
    return (
        <div className={cn('grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-3 xl:grid-cols-4', className)}>
            {products.map((product) => (
                <ProductCard key={product.id} product={product} />
            ))}
        </div>
    );
}
