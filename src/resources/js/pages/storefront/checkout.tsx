import { DateTimePicker } from '@/components/custom/date-picker';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { AlertTriangle, Clock, Loader2, MailWarning, Store, Truck, Users } from 'lucide-react';
import { lazy, Suspense, useEffect, useState } from 'react';

import { CartLineDetails } from '@/components/storefront/cart-line-details';
import { CartTotals, CouponBox } from '@/components/storefront/cart-parts';
import { PageHeader } from '@/components/storefront/page-header';
import { formatAddress, PsgcAddressFields, type PsgcAddress } from '@/components/storefront/psgc-address-fields';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useCart } from '@/hooks/use-cart';
import { jsonRequest } from '@/lib/http';
import { money } from '@/lib/format';
import { cn } from '@/lib/utils';
import verification from '@/routes/verification';

const MapPinPicker = lazy(() => import('@/components/storefront/map-pin-picker').then((m) => ({ default: m.MapPinPicker })));

interface Props {
    prefill: PsgcAddress & { customer_name: string; customer_email: string; customer_phone: string };
    emailVerified: boolean;
    store: { lat: number; lng: number; address: string };
    paymentMethods: { value: string; label: string; description: string }[];
}

interface Quote {
    fee: number;
    provider: 'lalamove' | 'estimate';
    service: string | null;
    distance_km: number | null;
    note: string | null;
}

function Section({ step, title, children }: { step: number; title: string; children: React.ReactNode }) {
    return (
        <section className="rounded-2xl border border-primary/20 bg-card p-5 shadow-sm sm:p-6">
            <h2 className="mb-4 flex items-center gap-3 text-lg font-bold text-foreground">
                <span className="flex size-7 items-center justify-center rounded-full bg-primary text-sm text-primary-foreground">{step}</span>
                {title}
            </h2>
            {children}
        </section>
    );
}

function Choice({
    active,
    onClick,
    icon: Icon,
    title,
    description,
}: {
    active: boolean;
    onClick: () => void;
    icon: typeof Truck;
    title: string;
    description: string;
}) {
    return (
        <button
            type="button"
            onClick={onClick}
            aria-pressed={active}
            className={cn(
                'flex flex-1 items-start gap-3 rounded-xl border-2 p-4 text-left transition-all',
                active ? 'border-primary bg-highlight/15' : 'border-primary/20 hover:border-primary/50',
            )}
        >
            <Icon className={cn('mt-0.5 size-5 shrink-0', active ? 'text-primary' : 'text-muted-foreground')} />
            <span>
                <span className="block text-sm font-bold text-foreground">{title}</span>
                <span className="block text-xs text-muted-foreground">{description}</span>
            </span>
        </button>
    );
}

const Err = ({ message }: { message?: string }) => (message ? <p className="text-xs text-destructive">{message}</p> : null);

export default function Checkout({ prefill, emailVerified, store, paymentMethods }: Props) {
    const { cart } = useCart();
    const form = useForm({
        ...prefill,
        fulfillment_type: 'delivery' as 'delivery' | 'pickup',
        delivery_mode: 'priority' as 'priority' | 'pooling',
        payment_method: paymentMethods[0]?.value ?? 'gcash',
        lat: null as number | null,
        lng: null as number | null,
        scheduled_time: '',
        notes: '',
        save_address: true,
    });
    const [quote, setQuote] = useState<{ key: string; value: Quote | null; error?: string } | null>(null);

    const d = form.data;
    const delivery = d.fulfillment_type === 'delivery';
    const priority = delivery && d.delivery_mode === 'priority';
    const address = formatAddress(d);
    const addressComplete = !!(d.city && d.barangay && d.street_address);
    const quoteKey = priority && addressComplete ? `${address}|${d.lat ?? ''}|${d.lng ?? ''}` : null;
    const currentQuote = quote && quote.key === quoteKey ? quote : null;
    const quoting = quoteKey !== null && currentQuote === null;

    // Server-side delivery quote for the current address / pin (debounced).
    useEffect(() => {
        if (!quoteKey) return;
        const controller = new AbortController();
        const id = window.setTimeout(() => {
            jsonRequest<Quote>('/checkout/quote', { method: 'POST', body: { address, lat: d.lat, lng: d.lng }, signal: controller.signal })
                .then((value) => setQuote({ key: quoteKey, value }))
                .catch((e: unknown) => {
                    if (!(e instanceof DOMException && e.name === 'AbortError'))
                        setQuote({ key: quoteKey, value: null, error: 'Could not calculate the delivery fee. Check the address and try again.' });
                });
        }, 500);
        return () => {
            window.clearTimeout(id);
            controller.abort();
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [quoteKey]);

    const deliveryFee = !delivery || d.delivery_mode === 'pooling' ? 0 : (currentQuote?.value?.fee ?? null);
    const canSubmit = emailVerified && cart.items.length > 0 && !form.processing && (!priority || deliveryFee !== null);

    const [minSchedule] = useState(() => new Date(Date.now() + 2 * 3600_000 - new Date().getTimezoneOffset() * 60_000).toISOString().slice(0, 16));

    return (
        <>
            <Head title="Checkout" />
            <div className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <PageHeader title="Checkout" />

                {!emailVerified && (
                    <div className="mb-6 flex flex-wrap items-center gap-3 rounded-2xl border border-warning/30 bg-warning/10 p-4 text-sm text-warning-ink">
                        <MailWarning className="size-5 shrink-0" />
                        <span className="flex-1">
                            Please verify your email before placing an order — we send your receipt and order updates there.
                        </span>
                        <Button size="sm" variant="outline" onClick={() => router.post(verification.send().url, {}, { preserveScroll: true })}>
                            Send verification link
                        </Button>
                    </div>
                )}

                <form
                    className="grid gap-8 lg:grid-cols-12"
                    onSubmit={(e) => {
                        e.preventDefault();
                        if (canSubmit) form.post('/checkout', { preserveScroll: true });
                    }}
                >
                    <div className="space-y-6 lg:col-span-7">
                        <Section step={1} title="Contact details">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-1.5 sm:col-span-2">
                                    <Label htmlFor="customer_name">Full name</Label>
                                    <Input
                                        id="customer_name"
                                        value={d.customer_name}
                                        onChange={(e) => form.setData('customer_name', e.target.value)}
                                        autoComplete="name"
                                        required
                                    />
                                    <Err message={form.errors.customer_name} />
                                </div>
                                <div className="space-y-1.5">
                                    <Label htmlFor="customer_email">Email</Label>
                                    <Input
                                        id="customer_email"
                                        type="email"
                                        value={d.customer_email}
                                        onChange={(e) => form.setData('customer_email', e.target.value)}
                                        autoComplete="email"
                                        required
                                    />
                                    <Err message={form.errors.customer_email} />
                                </div>
                                <div className="space-y-1.5">
                                    <Label htmlFor="customer_phone">Mobile number</Label>
                                    <Input
                                        type="tel"
                                        id="customer_phone"
                                        value={d.customer_phone}
                                        onChange={(e) => form.setData('customer_phone', e.target.value)}
                                        placeholder="09171234567"
                                        autoComplete="tel"
                                        required
                                    />
                                    <Err message={form.errors.customer_phone} />
                                </div>
                            </div>
                        </Section>

                        <Section step={2} title="Delivery or pickup">
                            <div className="flex flex-col gap-3 sm:flex-row">
                                <Choice
                                    active={delivery}
                                    onClick={() => form.setData('fulfillment_type', 'delivery')}
                                    icon={Truck}
                                    title="Deliver to me"
                                    description="Lalamove to your door"
                                />
                                <Choice
                                    active={!delivery}
                                    onClick={() => form.setData('fulfillment_type', 'pickup')}
                                    icon={Store}
                                    title="Store pickup"
                                    description={`Free · ${store.address}`}
                                />
                            </div>

                            {delivery && (
                                <div className="mt-6 space-y-6">
                                    <div className="flex flex-col gap-3 sm:flex-row">
                                        <Choice
                                            active={d.delivery_mode === 'priority'}
                                            onClick={() => form.setData('delivery_mode', 'priority')}
                                            icon={Clock}
                                            title="Priority delivery"
                                            description="Your own rider, fee shown now"
                                        />
                                        <Choice
                                            active={d.delivery_mode === 'pooling'}
                                            onClick={() => form.setData('delivery_mode', 'pooling')}
                                            icon={Users}
                                            title="Group delivery"
                                            description="Share a rider with nearby orders — cheaper, slower"
                                        />
                                    </div>
                                    {d.delivery_mode === 'pooling' && (
                                        <p className="rounded-xl bg-info/10 p-3 text-xs text-info-ink">
                                            We’ll batch your order with others in your area and assign a shared delivery fee. You pay once the fee is
                                            set — we’ll notify you.
                                        </p>
                                    )}
                                    <PsgcAddressFields value={d} onChange={(next) => form.setData({ ...d, ...next })} errors={form.errors} />
                                    <Suspense fallback={<div className="h-72 animate-pulse rounded-2xl bg-background" />}>
                                        <MapPinPicker
                                            store={store}
                                            value={d.lat && d.lng ? { lat: d.lat, lng: d.lng } : null}
                                            onChange={(p) => form.setData({ ...d, lat: p.lat, lng: p.lng })}
                                        />
                                    </Suspense>
                                    <label className="flex items-center gap-2 text-xs text-muted-foreground">
                                        <input
                                            type="checkbox"
                                            className="accent-primary"
                                            checked={d.save_address}
                                            onChange={(e) => form.setData('save_address', e.target.checked)}
                                        />
                                        Save as my default address
                                    </label>
                                </div>
                            )}
                        </Section>

                        <Section step={3} title="When & notes">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-1.5">
                                    <Label htmlFor="scheduled_time">Preferred date & time (optional)</Label>
                                    <DateTimePicker
                                        id="scheduled_time"
                                        min={minSchedule}
                                        value={d.scheduled_time}
                                        invalid={!!form.errors.scheduled_time}
                                        placeholder="Any time"
                                        onChange={(value) => form.setData('scheduled_time', value)}
                                    />
                                    <Err message={form.errors.scheduled_time} />
                                </div>
                                <div className="space-y-1.5 sm:col-span-2">
                                    <Label htmlFor="notes">Notes for the bakery</Label>
                                    <Textarea
                                        id="notes"
                                        value={d.notes}
                                        maxLength={1000}
                                        onChange={(e) => form.setData('notes', e.target.value)}
                                        placeholder="Allergies, gate code, dedication…"
                                    />
                                </div>
                            </div>
                        </Section>

                        <Section step={4} title="Payment">
                            <div className="grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="Payment method">
                                {paymentMethods.map((method) => (
                                    <label
                                        key={method.value}
                                        className={cn(
                                            'flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition-all',
                                            d.payment_method === method.value
                                                ? 'border-primary bg-highlight/15'
                                                : 'border-primary/20 hover:border-primary/50',
                                        )}
                                    >
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value={method.value}
                                            checked={d.payment_method === method.value}
                                            onChange={() => form.setData('payment_method', method.value)}
                                            className="mt-1 accent-primary"
                                        />
                                        <span>
                                            <span className="block text-sm font-bold text-foreground">{method.label}</span>
                                            <span className="block text-xs text-muted-foreground">{method.description}</span>
                                        </span>
                                    </label>
                                ))}
                            </div>
                            <Err message={form.errors.payment_method} />
                        </Section>
                    </div>

                    <aside className="lg:col-span-5">
                        <div className="sticky top-24 space-y-5 rounded-2xl border border-primary/20 bg-card p-6 shadow-sm">
                            <h2 className="text-lg font-bold text-foreground">Your order</h2>
                            <ul className="max-h-72 divide-y divide-primary/15 overflow-y-auto">
                                {cart.items.map((line) => (
                                    <li key={line.id} className="flex gap-3 py-3">
                                        <img src={line.image_url ?? '/images/logo.png'} alt="" className="size-12 rounded-lg object-cover" />
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-bold text-foreground">{line.name}</p>
                                            <CartLineDetails line={line} />
                                            <p className="font-mono text-xs text-muted-foreground">
                                                {line.qty} × {money(line.unit_price)}
                                            </p>
                                        </div>
                                        <span className="font-mono text-sm font-bold text-foreground">{money(line.subtotal)}</span>
                                    </li>
                                ))}
                            </ul>
                            <CouponBox />

                            {priority && (
                                <div className="text-xs">
                                    {!addressComplete && (
                                        <p className="text-muted-foreground">Enter your street, barangay and city to see the delivery fee.</p>
                                    )}
                                    {quoting && (
                                        <p className="flex items-center gap-1.5 text-muted-foreground">
                                            <Loader2 className="size-3.5 animate-spin" /> Calculating delivery fee…
                                        </p>
                                    )}
                                    {currentQuote?.value && (
                                        <p className="text-muted-foreground">
                                            {currentQuote.value.provider === 'lalamove'
                                                ? `Lalamove ${currentQuote.value.service ?? ''}`
                                                : 'Estimated'}{' '}
                                            · {currentQuote.value.distance_km ?? '—'} km
                                            {currentQuote.value.note && <span className="block">{currentQuote.value.note}</span>}
                                        </p>
                                    )}
                                    {currentQuote?.error && (
                                        <p className="flex items-center gap-1.5 text-destructive">
                                            <AlertTriangle className="size-3.5" /> {currentQuote.error}
                                        </p>
                                    )}
                                </div>
                            )}

                            <CartTotals
                                deliveryFee={d.delivery_mode === 'pooling' && delivery ? 0 : deliveryFee}
                                label={d.delivery_mode === 'pooling' && delivery ? 'Total before shared delivery' : 'Total'}
                            />

                            <Button type="submit" size="lg" className="w-full" disabled={!canSubmit}>
                                {form.processing ? <Loader2 className="size-4 animate-spin" /> : null}
                                Place order • {money(cart.total + (deliveryFee ?? 0))}
                            </Button>
                            <p className="text-center text-[11px] text-muted-foreground">
                                By placing your order you agree to our{' '}
                                <Link href="/terms" className="underline">
                                    terms
                                </Link>
                                . Prices are confirmed by the bakery at checkout.
                            </p>
                        </div>
                    </aside>
                </form>
            </div>
        </>
    );
}
