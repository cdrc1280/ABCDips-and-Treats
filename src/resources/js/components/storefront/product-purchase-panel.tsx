import { router } from '@inertiajs/react';
import { AlertTriangle, Check, ChevronLeft, ChevronRight, Clock, Minus, Plus, ShoppingBag, Star } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';

import { ProductPhoto } from '@/components/storefront/product-card';
import { WishlistButton } from '@/components/storefront/wishlist-button';
import { Button } from '@/components/ui/button';
import { useCart } from '@/hooks/use-cart';
import { money } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { CartLine, CartLineOptions, ProductDetailData } from '@/types';

/** Image carousel with thumbnails; no photo (or a broken one) shows the designed parchment stand-in. */
export function ProductGallery({ product }: { product: ProductDetailData }) {
    const images: (string | null)[] = product.images.length ? product.images : [product.image_url];
    const [index, setIndex] = useState(0);
    const [touchX, setTouchX] = useState<number | null>(null);
    const go = (next: number) => setIndex(Math.max(0, Math.min(images.length - 1, next)));

    return (
        <div className="space-y-3">
            <div
                className="relative aspect-square overflow-hidden rounded-2xl border border-primary/20 bg-background/50 select-none"
                onTouchStart={(event) => setTouchX(event.touches[0].clientX)}
                onTouchEnd={(event) => {
                    if (touchX === null) return;
                    const delta = event.changedTouches[0].clientX - touchX;
                    if (Math.abs(delta) > 40) go(index + (delta < 0 ? 1 : -1));
                    setTouchX(null);
                }}
            >
                <ProductPhoto
                    key={images[index] ?? 'none'}
                    src={images[index]}
                    name={product.name}
                    categorySlug={product.category?.slug}
                    eager
                    className="animate-in duration-300 fade-in"
                />
                {images.length > 1 && (
                    <>
                        {index > 0 && (
                            <button
                                type="button"
                                aria-label="Previous image"
                                onClick={() => go(index - 1)}
                                className="absolute top-1/2 left-2 flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60"
                            >
                                <ChevronLeft className="size-4" />
                            </button>
                        )}
                        {index < images.length - 1 && (
                            <button
                                type="button"
                                aria-label="Next image"
                                onClick={() => go(index + 1)}
                                className="absolute top-1/2 right-2 flex size-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60"
                            >
                                <ChevronRight className="size-4" />
                            </button>
                        )}
                        <div className="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-1.5">
                            {images.map((src, i) => (
                                <button
                                    key={src ?? i}
                                    type="button"
                                    aria-label={`Show image ${i + 1}`}
                                    onClick={() => setIndex(i)}
                                    className={cn('h-2 rounded-full transition-all', i === index ? 'w-5 bg-white' : 'w-2 bg-white/50')}
                                />
                            ))}
                        </div>
                    </>
                )}
            </div>
            {images.length > 1 && (
                <div className="no-scrollbar flex gap-2 overflow-x-auto px-1 pb-1">
                    {images.map((src, i) => (
                        <button
                            key={src ?? i}
                            type="button"
                            onClick={() => setIndex(i)}
                            className={cn(
                                'size-16 shrink-0 overflow-hidden rounded-xl border-2 transition-all',
                                i === index ? 'scale-95 border-primary shadow-md' : 'border-primary/20 opacity-60 hover:opacity-100',
                            )}
                        >
                            <img src={src ?? undefined} alt="" loading="lazy" className="size-full object-cover" />
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

function Rating({ product }: { product: ProductDetailData }) {
    if (!product.reviews_count) return <span className="text-xs text-muted-foreground">No reviews yet</span>;
    return (
        <span className="flex items-center gap-1 text-sm">
            {[1, 2, 3, 4, 5].map((n) => (
                <Star
                    key={n}
                    className={cn('size-4', n <= Math.round(product.avg_rating ?? 0) ? 'fill-highlight text-highlight' : 'text-muted-foreground/40')}
                />
            ))}
            <span className="ml-1 font-mono font-bold text-foreground">{product.avg_rating}</span>
            <span className="text-muted-foreground">({product.reviews_count} reviews)</span>
        </span>
    );
}

function OptionChip({
    active,
    onClick,
    children,
    disabled,
}: {
    active: boolean;
    onClick: () => void;
    children: React.ReactNode;
    disabled?: boolean;
}) {
    return (
        <button
            type="button"
            onClick={onClick}
            disabled={disabled}
            aria-pressed={active}
            className={cn(
                'flex items-center gap-1.5 rounded-xl border px-3 py-2 text-xs font-bold transition-all disabled:cursor-not-allowed disabled:opacity-40 sm:text-sm',
                active
                    ? 'border-primary bg-primary text-primary-foreground shadow-sm'
                    : 'border-primary/30 bg-card text-foreground hover:border-primary',
            )}
        >
            {active && <Check className="size-3.5" />}
            {children}
        </button>
    );
}

const modifierLabel = (value: number) => (value ? ` (${value > 0 ? '+' : '−'}${money(Math.abs(value))})` : '');

/**
 * Variation / flavor / quantity selection and add-to-basket. Prices shown here are a preview;
 * the server re-prices every line from the product (App\Domains\Storefront\Support\CartPricing).
 */
/**
 * Options, quantity and the add/buy buttons. With `stickyBar` (product page), phones get a fixed
 * "Add to basket" bar once the main buttons scroll out of view; it sets --bottom-bar-h so the chat bubble moves up.
 */
export function ProductPurchasePanel({
    product,
    editLine,
    onDone,
    stickyBar = false,
}: {
    product: ProductDetailData;
    editLine?: CartLine;
    onDone?: () => void;
    stickyBar?: boolean;
}) {
    const { add, update } = useCart();
    const actionsRef = useRef<HTMLDivElement>(null);
    const [actionsOffscreen, setActionsOffscreen] = useState(false);
    const initial = editLine?.options ?? {};
    const [variation, setVariation] = useState<string | null>(initial.variation ?? null);
    const [flavor, setFlavor] = useState<string | null>(initial.flavor && !initial.flavors ? initial.flavor : null);
    const [flavors, setFlavors] = useState<string[]>(Array.isArray(initial.flavors) ? initial.flavors : []);
    const [qty, setQty] = useState(editLine?.qty ?? 1);
    const [busy, setBusy] = useState(false);

    const multi = product.is_assorted && product.flavors.length > 0;
    const maxFlavors = product.max_flavors ?? product.flavors.length;
    const maxQty = Math.max(1, Math.min(99, product.stock_qty || 99));

    const variationMod = product.variations.find((v) => v.label === variation)?.price_modifier ?? 0;
    const flavorMod = multi
        ? product.flavors.filter((f) => flavors.includes(f.name)).reduce((sum, f) => sum + f.price_modifier, 0)
        : (product.flavors.find((f) => f.name === flavor)?.price_modifier ?? 0);
    const unit = product.effective_price + variationMod + flavorMod;

    const missing =
        (product.variations.length > 0 && !variation && 'Choose an option') ||
        (multi && flavors.length === 0 && 'Pick at least one flavor') ||
        (!multi && product.flavors.length > 1 && !flavor && 'Choose a flavor') ||
        null;

    const options = (): CartLineOptions | null => {
        const out: CartLineOptions = {};
        if (variation) out.variation = variation;
        if (multi) out.flavors = flavors;
        else if (flavor ?? product.flavors[0]?.name) out.flavor = flavor ?? product.flavors[0]?.name;
        return Object.keys(out).length ? out : null;
    };

    const submit = (thenCheckout = false) => {
        if (missing || !product.is_in_stock) return;
        setBusy(true);
        const finish = () => {
            setBusy(false);
            onDone?.();
            if (thenCheckout) router.visit('/checkout');
        };
        if (editLine) {
            update(editLine.id, qty, options() ?? {});
            finish();
        } else {
            add(product.id, qty, options(), finish);
        }
    };

    useEffect(() => {
        const node = actionsRef.current;
        if (!stickyBar || !node || !('IntersectionObserver' in window)) return;
        const observer = new IntersectionObserver(([entry]) => setActionsOffscreen(!entry.isIntersecting));
        observer.observe(node);
        return () => observer.disconnect();
    }, [stickyBar]);

    const showBar = stickyBar && actionsOffscreen;
    useEffect(() => {
        if (!showBar) return;
        const root = document.documentElement;
        root.style.setProperty('--bottom-bar-h', '4.5rem');
        return () => {
            root.style.removeProperty('--bottom-bar-h');
        };
    }, [showBar]);

    const toggleFlavor = (name: string) =>
        setFlavors((list) => (list.includes(name) ? list.filter((f) => f !== name) : list.length < maxFlavors ? [...list, name] : list));

    return (
        <div className="space-y-5">
            <div>
                {product.category && <p className="text-xs font-bold tracking-widest text-primary uppercase">{product.category.name}</p>}
                <h2 className="mt-1 font-display text-2xl text-foreground sm:text-3xl">{product.name}</h2>
                <div className="mt-2 flex flex-wrap items-center gap-3">
                    <Rating product={product} />
                    {product.prep_time_minutes ? (
                        <span className="flex items-center gap-1 text-xs text-muted-foreground">
                            <Clock className="size-3.5 text-primary" /> {product.prep_time_minutes} min prep
                        </span>
                    ) : null}
                </div>
            </div>

            <div className="flex items-baseline gap-3 rounded-2xl border border-primary/20 bg-background px-4 py-3">
                <span className="font-display font-mono text-3xl text-primary tabular-nums">{money(unit)}</span>
                {product.is_on_sale && (
                    <span className="font-mono text-sm text-muted-foreground line-through">{money(product.price + variationMod + flavorMod)}</span>
                )}
                {product.is_on_sale && (
                    <span className="rounded-full bg-destructive px-2 py-0.5 text-[10px] font-bold text-destructive-foreground uppercase">Sale</span>
                )}
            </div>

            {product.short_description && <p className="text-sm leading-relaxed text-muted-foreground">{product.short_description}</p>}

            {product.variations.length > 0 && (
                <fieldset>
                    <legend className="mb-2 text-sm font-bold text-foreground capitalize">
                        {product.variation_type !== 'none' ? product.variation_type : 'Option'}
                    </legend>
                    <div className="flex flex-wrap gap-2">
                        {product.variations.map((v) => (
                            <OptionChip key={v.label} active={variation === v.label} onClick={() => setVariation(v.label)}>
                                {v.label}
                                {modifierLabel(v.price_modifier)}
                            </OptionChip>
                        ))}
                    </div>
                </fieldset>
            )}

            {product.flavors.length > (multi ? 0 : 1) && (
                <fieldset>
                    <legend className="mb-2 text-sm font-bold text-foreground">
                        {multi ? `Pick your flavors (${flavors.length}/${maxFlavors})` : 'Flavor'}
                    </legend>
                    <div className="flex flex-wrap gap-2">
                        {product.flavors.map((f) => {
                            const active = multi ? flavors.includes(f.name) : flavor === f.name;
                            return (
                                <OptionChip
                                    key={f.name}
                                    active={active}
                                    disabled={multi && !active && flavors.length >= maxFlavors}
                                    onClick={() => (multi ? toggleFlavor(f.name) : setFlavor(f.name))}
                                >
                                    {f.name}
                                    {modifierLabel(f.price_modifier)}
                                </OptionChip>
                            );
                        })}
                    </div>
                </fieldset>
            )}

            <div className="flex flex-wrap items-center gap-4">
                <div className="flex items-center rounded-xl border border-primary/30 bg-card">
                    <button
                        type="button"
                        aria-label="Decrease quantity"
                        onClick={() => setQty((q) => Math.max(1, q - 1))}
                        className="p-2.5 text-primary disabled:opacity-40"
                        disabled={qty <= 1}
                    >
                        <Minus className="size-4" />
                    </button>
                    <span className="w-10 text-center font-mono font-bold text-foreground tabular-nums" aria-live="polite">
                        {qty}
                    </span>
                    <button
                        type="button"
                        aria-label="Increase quantity"
                        onClick={() => setQty((q) => Math.min(maxQty, q + 1))}
                        className="p-2.5 text-primary disabled:opacity-40"
                        disabled={qty >= maxQty}
                    >
                        <Plus className="size-4" />
                    </button>
                </div>
                {product.is_low_stock && (
                    <span className="flex items-center gap-1 text-xs font-bold text-warning-ink">
                        <AlertTriangle className="size-3.5" /> Only {product.stock_qty} left
                    </span>
                )}
                {!product.is_in_stock && <span className="text-sm font-bold text-destructive">Sold out — check back soon</span>}
            </div>

            {missing && <p className="text-xs text-muted-foreground">{missing} to continue.</p>}

            <div ref={actionsRef} className="flex flex-col gap-2 sm:flex-row">
                <Button size="lg" className="flex-1" disabled={busy || !!missing || !product.is_in_stock} onClick={() => submit()}>
                    <ShoppingBag className="size-4" />
                    {editLine ? 'Update basket' : `Add to basket • ${money(unit * qty)}`}
                </Button>
                {!editLine && (
                    <Button
                        size="lg"
                        variant="outline"
                        className="flex-1"
                        disabled={busy || !!missing || !product.is_in_stock}
                        onClick={() => submit(true)}
                    >
                        Buy now
                    </Button>
                )}
            </div>

            <div className="flex justify-end border-t border-primary/15 pt-3">
                <WishlistButton productId={product.id} withLabel />
            </div>

            {showBar && (
                <div className="fixed inset-x-0 bottom-0 z-40 flex h-18 animate-in items-center gap-3 border-t border-border bg-card/95 px-4 pb-[env(safe-area-inset-bottom)] shadow-lg backdrop-blur-md duration-200 slide-in-from-bottom-4 lg:hidden">
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-semibold text-foreground">{product.name}</p>
                        <p className="font-mono text-sm text-primary tabular-nums">{money(unit * qty)}</p>
                    </div>
                    {missing ? (
                        <Button
                            size="lg"
                            variant="outline"
                            onClick={() => actionsRef.current?.scrollIntoView({ behavior: 'smooth', block: 'center' })}
                        >
                            {missing}
                        </Button>
                    ) : (
                        <Button size="lg" disabled={busy || !product.is_in_stock} onClick={() => submit()}>
                            <ShoppingBag className="size-4" /> {product.is_in_stock ? 'Add to basket' : 'Sold out'}
                        </Button>
                    )}
                </div>
            )}
        </div>
    );
}
