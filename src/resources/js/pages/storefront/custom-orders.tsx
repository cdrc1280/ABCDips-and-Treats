import { DatePicker } from '@/components/custom/date-picker';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { Cake, ImagePlus, Send, ShoppingBag, X } from 'lucide-react';
import { useState } from 'react';

import { PageHeader } from '@/components/storefront/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useCart } from '@/hooks/use-cart';
import { money } from '@/lib/format';
import { login } from '@/routes';

interface Props {
    prefill: { customer_name: string; customer_email: string; customer_phone: string };
    pricing: { tiers: Record<string, number>; surcharges: { min_servings: number; amount: number }[] };
    cakeProductId: string | null;
}

const FLAVORS = ['Signature Ube Halaya', 'Belgian Chocolate', 'Classic Vanilla', 'Red Velvet', 'Mocha', 'Banana Walnut'];
const FROSTINGS = ['Silky Cream Cheese', 'Swiss Meringue Buttercream', 'Chocolate Ganache', 'Whipped Cream', 'Fondant'];

/** Same formula as App\Domains\Storefront\Support\CustomCakePricing (data comes from the server). */
function estimate(pricing: Props['pricing'], tiers: number, servings: number): number {
    const keys = Object.keys(pricing.tiers).map(Number);
    const tier = Math.min(Math.max(1, tiers), Math.max(...keys));
    return pricing.tiers[tier] + pricing.surcharges.filter((s) => servings >= s.min_servings).reduce((sum, s) => sum + s.amount, 0);
}

const Err = ({ message }: { message?: string }) => (message ? <p className="text-xs text-destructive">{message}</p> : null);

export default function CustomOrders({ prefill, pricing, cakeProductId }: Props) {
    const user = usePage().props.auth.user;
    const { add } = useCart();
    const [minDate] = useState(() => new Date(Date.now() + 2 * 86400_000).toISOString().slice(0, 10));
    const form = useForm({
        ...prefill,
        event_date: minDate,
        servings_count: 30,
        tiers_count: 2,
        flavor_preference: FLAVORS[0],
        frosting_type: FROSTINGS[0],
        cake_inscription: '',
        theme_description: '',
        budget_range_min: '' as number | '',
        budget_range_max: '' as number | '',
        reference_photos: [] as File[],
    });
    const d = form.data;
    const price = estimate(pricing, Number(d.tiers_count), Number(d.servings_count));

    const addToBasket = () =>
        cakeProductId &&
        add(cakeProductId, 1, {
            is_custom: true,
            custom_title: `Custom ${d.tiers_count}-Tier ${d.flavor_preference} Cake`,
            tiers_count: Number(d.tiers_count),
            servings_count: Number(d.servings_count),
            flavor_preference: d.flavor_preference,
            frosting_type: d.frosting_type,
            cake_inscription: d.cake_inscription,
            event_date: d.event_date,
            theme_description: d.theme_description,
        });

    return (
        <>
            <Head title="Custom Cakes" />
            <div className="mx-auto max-w-6xl px-4 pb-20 sm:px-6 lg:px-8">
                <PageHeader
                    title="Custom cakes"
                    subtitle="Birthdays, weddings, christenings. Tell us the occasion and the design, and we'll bake it."
                />

                <form
                    className="grid gap-8 lg:grid-cols-12"
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post('/custom-orders', {
                            forceFormData: true,
                            preserveScroll: true,
                            onSuccess: () => form.reset('theme_description', 'cake_inscription', 'reference_photos'),
                        });
                    }}
                >
                    <div className="space-y-6 rounded-2xl border border-primary/20 bg-card p-6 shadow-sm lg:col-span-8">
                        <div className="grid gap-4 sm:grid-cols-3">
                            <div className="space-y-1.5 sm:col-span-3">
                                <Label htmlFor="customer_name">Your name</Label>
                                <Input
                                    id="customer_name"
                                    value={d.customer_name}
                                    onChange={(e) => form.setData('customer_name', e.target.value)}
                                    required
                                />
                                <Err message={form.errors.customer_name} />
                            </div>
                            <div className="space-y-1.5 sm:col-span-2">
                                <Label htmlFor="customer_email">Email</Label>
                                <Input
                                    id="customer_email"
                                    type="email"
                                    value={d.customer_email}
                                    onChange={(e) => form.setData('customer_email', e.target.value)}
                                    required
                                />
                                <Err message={form.errors.customer_email} />
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="customer_phone">Mobile</Label>
                                <Input
                                    type="tel"
                                    id="customer_phone"
                                    value={d.customer_phone}
                                    onChange={(e) => form.setData('customer_phone', e.target.value)}
                                    placeholder="09171234567"
                                    required
                                />
                                <Err message={form.errors.customer_phone} />
                            </div>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-3">
                            <div className="space-y-1.5">
                                <Label htmlFor="event_date">Event date</Label>
                                <DatePicker
                                    id="event_date"
                                    min={minDate}
                                    value={d.event_date}
                                    clearable={false}
                                    invalid={!!form.errors.event_date}
                                    onChange={(value) => form.setData('event_date', value ?? minDate)}
                                />
                                <Err message={form.errors.event_date} />
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="tiers_count">Tiers</Label>
                                <Input
                                    id="tiers_count"
                                    type="number"
                                    min={1}
                                    max={10}
                                    value={d.tiers_count}
                                    onChange={(e) => form.setData('tiers_count', Number(e.target.value))}
                                />
                                <Err message={form.errors.tiers_count} />
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="servings_count">Guests / servings</Label>
                                <Input
                                    id="servings_count"
                                    type="number"
                                    min={1}
                                    max={1000}
                                    value={d.servings_count}
                                    onChange={(e) => form.setData('servings_count', Number(e.target.value))}
                                />
                                <Err message={form.errors.servings_count} />
                            </div>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            {(
                                [
                                    ['flavor_preference', 'Cake flavor', FLAVORS],
                                    ['frosting_type', 'Frosting', FROSTINGS],
                                ] as const
                            ).map(([field, label, list]) => (
                                <div key={field} className="space-y-1.5">
                                    <Label htmlFor={field}>{label}</Label>
                                    <Input id={field} list={`${field}-list`} value={d[field]} onChange={(e) => form.setData(field, e.target.value)} />
                                    <datalist id={`${field}-list`}>
                                        {list.map((item) => (
                                            <option key={item} value={item} />
                                        ))}
                                    </datalist>
                                </div>
                            ))}
                        </div>

                        <div className="space-y-1.5">
                            <Label htmlFor="cake_inscription">Message on the cake (optional)</Label>
                            <Input
                                id="cake_inscription"
                                maxLength={120}
                                value={d.cake_inscription}
                                onChange={(e) => form.setData('cake_inscription', e.target.value)}
                                placeholder="Happy 7th Birthday, Mia!"
                            />
                        </div>
                        <div className="space-y-1.5">
                            <Label htmlFor="theme_description">Theme & design</Label>
                            <Textarea
                                id="theme_description"
                                className="min-h-28"
                                value={d.theme_description}
                                onChange={(e) => form.setData('theme_description', e.target.value)}
                                placeholder="Colours, characters, toppers, florals…"
                                required
                            />
                            <Err message={form.errors.theme_description} />
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <Label htmlFor="budget_range_min">Budget from (₱, optional)</Label>
                                <Input
                                    id="budget_range_min"
                                    type="number"
                                    min={0}
                                    value={d.budget_range_min}
                                    onChange={(e) => form.setData('budget_range_min', e.target.value === '' ? '' : Number(e.target.value))}
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="budget_range_max">Budget to (₱, optional)</Label>
                                <Input
                                    id="budget_range_max"
                                    type="number"
                                    min={0}
                                    value={d.budget_range_max}
                                    onChange={(e) => form.setData('budget_range_max', e.target.value === '' ? '' : Number(e.target.value))}
                                />
                                <Err message={form.errors.budget_range_max} />
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-2">
                            <label className="flex cursor-pointer items-center gap-1.5 rounded-xl border border-primary/30 px-3 py-2 text-xs font-bold text-primary hover:bg-highlight/20">
                                <ImagePlus className="size-4" /> Reference photos (up to 5)
                                <input
                                    type="file"
                                    multiple
                                    accept="image/jpeg,image/png,image/webp"
                                    className="sr-only"
                                    onChange={(e) =>
                                        form.setData('reference_photos', [...d.reference_photos, ...Array.from(e.target.files ?? [])].slice(0, 5))
                                    }
                                />
                            </label>
                            {d.reference_photos.map((file, i) => (
                                <span key={`${file.name}-${i}`} className="flex items-center gap-1 rounded-lg bg-background px-2 py-1 text-xs">
                                    {file.name}
                                    <button
                                        type="button"
                                        aria-label={`Remove ${file.name}`}
                                        onClick={() =>
                                            form.setData(
                                                'reference_photos',
                                                d.reference_photos.filter((_, j) => j !== i),
                                            )
                                        }
                                    >
                                        <X className="size-3" />
                                    </button>
                                </span>
                            ))}
                        </div>
                        {form.errors['reference_photos.0'] && <Err message={form.errors['reference_photos.0']} />}
                    </div>

                    <aside className="lg:col-span-4">
                        <div className="sticky top-24 space-y-4 rounded-2xl border border-primary/30 bg-sidebar p-6 text-sidebar-foreground shadow-xl">
                            <Cake className="size-8 text-sidebar-primary" />
                            <div>
                                <p className="text-xs font-bold tracking-widest text-sidebar-primary uppercase">Estimated price</p>
                                <p className="font-mono text-4xl font-black">{money(price)}</p>
                                <p className="mt-1 text-xs text-sidebar-foreground/70">
                                    {d.tiers_count}-tier · {d.servings_count} servings. Final quote depends on design details.
                                </p>
                            </div>
                            {user ? (
                                <div className="space-y-2">
                                    <Button type="submit" className="w-full" variant="secondary" disabled={form.processing}>
                                        <Send className="size-4" /> Request a quote
                                    </Button>
                                    {cakeProductId && (
                                        <Button
                                            type="button"
                                            className="w-full border-sidebar-border bg-transparent text-sidebar-foreground hover:bg-sidebar-accent"
                                            variant="outline"
                                            onClick={addToBasket}
                                        >
                                            <ShoppingBag className="size-4" /> Add to basket at {money(price)}
                                        </Button>
                                    )}
                                </div>
                            ) : (
                                <Button asChild className="w-full" variant="secondary">
                                    <Link href={login().url}>Sign in to order</Link>
                                </Button>
                            )}
                            <p className="text-[11px] text-sidebar-foreground/60">
                                We need at least 2 days’ notice. Our pastry chef replies within 24 hours.
                            </p>
                        </div>
                    </aside>
                </form>
            </div>
        </>
    );
}
