import { Head, router, usePage } from '@inertiajs/react';
import {
    Banknote,
    CreditCard,
    ImageOff,
    Loader2,
    Minus,
    Plus,
    Printer,
    ScanBarcode,
    Search,
    ShoppingCart,
    Smartphone,
    Trash2,
    Wallet,
} from 'lucide-react';
import { useMemo, useRef, useState, type FormEvent } from 'react';
import { toast } from 'sonner';

import { InputError } from '@/components/custom/field';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { money } from '@/lib/format';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes/admin';
import { invoice } from '@/routes/order';
import { checkout } from '@/routes/admin/pos';
import type { PosReceipt } from '@/types';

interface PosProduct {
    id: string;
    name: string;
    sku: string;
    barcode: string | null;
    category: string | null;
    price: number;
    regular_price: number;
    stock: number;
    image_url: string | null;
}

type Method = 'cash' | 'gcash' | 'maya' | 'card';
const methods: { value: Method; label: string; icon: typeof Banknote }[] = [
    { value: 'cash', label: 'Cash', icon: Banknote },
    { value: 'gcash', label: 'GCash', icon: Smartphone },
    { value: 'maya', label: 'Maya', icon: Wallet },
    { value: 'card', label: 'Card', icon: CreditCard },
];

/** Walk-in register. Barcode scanners type the code + Enter into the scan box. */
export default function Pos({ products }: { products: PosProduct[] }) {
    const flash = usePage().flash as { receipt?: PosReceipt };
    const [query, setQuery] = useState('');
    const [category, setCategory] = useState<string | null>(null);
    const [cart, setCart] = useState<Record<string, number>>({});
    const [method, setMethod] = useState<Method>('cash');
    const [cash, setCash] = useState('');
    const [customer, setCustomer] = useState('');
    const [reference, setReference] = useState('');
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [processing, setProcessing] = useState(false);
    const [receipt, setReceipt] = useState<PosReceipt | null>(null);
    const [shownReceipt, setShownReceipt] = useState<string | null>(null);
    const scanRef = useRef<HTMLInputElement>(null);
    const saleRef = useRef<HTMLDivElement>(null);

    // Show the receipt flashed by the server once per sale.
    if (flash.receipt && flash.receipt.id !== shownReceipt) {
        setShownReceipt(flash.receipt.id);
        setReceipt(flash.receipt);
    }

    const byId = useMemo(() => new Map(products.map((p) => [p.id, p])), [products]);
    const categories = useMemo(() => [...new Set(products.map((p) => p.category).filter(Boolean))] as string[], [products]);
    const visible = products.filter(
        (p) =>
            (!category || p.category === category) && (!query || `${p.name} ${p.sku} ${p.barcode ?? ''}`.toLowerCase().includes(query.toLowerCase())),
    );

    const lines = Object.entries(cart)
        .map(([id, qty]) => ({ product: byId.get(id)!, qty }))
        .filter((line) => line.product);
    const total = Math.round(lines.reduce((sum, line) => sum + line.product.price * line.qty, 0) * 100) / 100;
    const cashValue = Number(cash || 0);
    const change = method === 'cash' ? Math.max(0, cashValue - total) : 0;
    const canCharge = lines.length > 0 && (method !== 'cash' || cashValue >= total) && !processing;

    const add = (product: PosProduct) => {
        const current = cart[product.id] ?? 0;
        if (current >= product.stock) return toast.warning(`Only ${product.stock} ${product.name} in stock.`);
        setCart({ ...cart, [product.id]: current + 1 });
    };
    const setQty = (id: string, qty: number) => {
        const product = byId.get(id);
        if (!product) return;
        const next = { ...cart };
        if (qty <= 0) delete next[id];
        else next[id] = Math.min(qty, product.stock);
        setCart(next);
    };

    const scan = (event: FormEvent) => {
        event.preventDefault();
        const code = query.trim().toLowerCase();
        const match = products.find((p) => p.barcode?.toLowerCase() === code || p.sku.toLowerCase() === code);
        if (match) {
            add(match);
            setQuery('');
        } else if (visible.length === 1) {
            add(visible[0]);
            setQuery('');
        }
    };

    const charge = () => {
        router.post(
            checkout().url,
            {
                payment_method: method,
                cash_tendered: method === 'cash' ? cash : '',
                customer_name: customer,
                reference,
                items: lines.map((line) => ({ id: line.product.id, qty: line.qty })),
            },
            {
                preserveScroll: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onError: (e) => setErrors(e),
                onSuccess: () => {
                    setCart({});
                    setCash('');
                    setCustomer('');
                    setReference('');
                    setErrors({});
                    scanRef.current?.focus();
                },
            },
        );
    };

    return (
        <>
            <Head title="POS register" />
            <div className="grid grid-cols-[minmax(0,1fr)] gap-4 pb-20 lg:grid-cols-[minmax(0,1fr)_24rem] lg:pb-0">
                <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                    <form onSubmit={scan} className="flex gap-2">
                        <div className="relative flex-1">
                            <ScanBarcode className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                ref={scanRef}
                                autoFocus
                                value={query}
                                onChange={(event) => setQuery(event.target.value)}
                                placeholder="Scan a barcode or search products…"
                                className="pl-9"
                                aria-label="Scan or search products"
                            />
                        </div>
                        <Button type="submit" variant="outline">
                            <Search /> Add
                        </Button>
                    </form>

                    {categories.length > 1 && (
                        <div className="flex gap-2 overflow-x-auto pb-1">
                            <Button size="sm" variant={category === null ? 'default' : 'outline'} onClick={() => setCategory(null)}>
                                All
                            </Button>
                            {categories.map((name) => (
                                <Button key={name} size="sm" variant={category === name ? 'default' : 'outline'} onClick={() => setCategory(name)}>
                                    {name}
                                </Button>
                            ))}
                        </div>
                    )}

                    <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
                        {visible.map((product) => {
                            const inCart = cart[product.id] ?? 0;
                            const soldOut = product.stock <= 0;
                            return (
                                <button
                                    key={product.id}
                                    type="button"
                                    disabled={soldOut}
                                    onClick={() => add(product)}
                                    className={cn(
                                        'group relative grid overflow-hidden rounded-xl border bg-card text-left shadow-xs transition hover:border-primary focus-visible:ring-2 focus-visible:ring-ring disabled:opacity-50',
                                        inCart > 0 && 'border-primary ring-1 ring-primary',
                                    )}
                                >
                                    <div className="flex aspect-[4/3] items-center justify-center bg-muted">
                                        {product.image_url ? (
                                            <img src={product.image_url} alt="" loading="lazy" className="size-full object-cover" />
                                        ) : (
                                            <ImageOff className="size-6 text-muted-foreground" />
                                        )}
                                    </div>
                                    <div className="grid gap-0.5 p-2.5">
                                        <span className="line-clamp-2 text-sm font-medium">{product.name}</span>
                                        <span className="flex items-center justify-between text-xs">
                                            <span className="font-semibold tabular-nums">{money(product.price)}</span>
                                            <span className={cn('tabular-nums', product.stock <= 5 ? 'text-destructive' : 'text-muted-foreground')}>
                                                {soldOut ? 'Sold out' : `${product.stock} left`}
                                            </span>
                                        </span>
                                    </div>
                                    {inCart > 0 && <Badge className="absolute top-2 right-2 tabular-nums">{inCart}</Badge>}
                                </button>
                            );
                        })}
                        {visible.length === 0 && <p className="col-span-full py-10 text-center text-sm text-muted-foreground">No products match.</p>}
                    </div>
                </div>

                <Card ref={saleRef} className="h-fit scroll-mt-20 lg:sticky lg:top-20">
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-base">
                            <ShoppingCart className="size-4" /> Current sale
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-4">
                        {lines.length === 0 ? (
                            <p className="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">
                                Tap a product or scan a barcode.
                            </p>
                        ) : (
                            <ul className="grid max-h-72 grid-cols-[minmax(0,1fr)] gap-2 overflow-y-auto">
                                {lines.map(({ product, qty }) => (
                                    <li key={product.id} className="flex flex-wrap items-center gap-x-2 gap-y-1">
                                        <div className="min-w-0 basis-full sm:flex-1 sm:basis-0">
                                            <p className="truncate text-sm font-medium">{product.name}</p>
                                            <p className="text-xs text-muted-foreground tabular-nums">{money(product.price)} each</p>
                                        </div>
                                        <div className="flex items-center gap-1">
                                            <Button
                                                size="icon"
                                                variant="outline"
                                                className="size-7"
                                                aria-label="Fewer"
                                                onClick={() => setQty(product.id, qty - 1)}
                                            >
                                                <Minus />
                                            </Button>
                                            <span className="w-7 text-center text-sm tabular-nums">{qty}</span>
                                            <Button
                                                size="icon"
                                                variant="outline"
                                                className="size-7"
                                                aria-label="More"
                                                onClick={() => setQty(product.id, qty + 1)}
                                            >
                                                <Plus />
                                            </Button>
                                        </div>
                                        <span className="ml-auto w-20 text-right text-sm tabular-nums">{money(product.price * qty)}</span>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            className="size-7 text-muted-foreground"
                                            aria-label="Remove"
                                            onClick={() => setQty(product.id, 0)}
                                        >
                                            <Trash2 />
                                        </Button>
                                    </li>
                                ))}
                            </ul>
                        )}
                        <InputError message={errors.items} />

                        <div className="flex items-center justify-between border-t pt-3 text-lg font-bold">
                            <span>Total</span>
                            <span className="tabular-nums">{money(total)}</span>
                        </div>

                        <div className="grid gap-1.5">
                            <Label>Payment</Label>
                            <ToggleGroup
                                type="single"
                                variant="outline"
                                value={method}
                                onValueChange={(value) => value && setMethod(value as Method)}
                                className="w-full"
                            >
                                {methods.map(({ value, label, icon: Icon }) => (
                                    <ToggleGroupItem key={value} value={value} className="flex-1" aria-label={label}>
                                        <Icon /> <span className="hidden sm:inline">{label}</span>
                                    </ToggleGroupItem>
                                ))}
                            </ToggleGroup>
                        </div>

                        {method === 'cash' ? (
                            <div className="grid gap-1.5">
                                <Label htmlFor="cash">Cash received</Label>
                                <Input
                                    id="cash"
                                    type="number"
                                    inputMode="decimal"
                                    min={0}
                                    step="0.01"
                                    value={cash}
                                    onChange={(event) => setCash(event.target.value)}
                                    aria-invalid={!!errors.cash_tendered}
                                />
                                <div className="flex flex-wrap gap-1.5">
                                    {[total, 100, 200, 500, 1000]
                                        .filter((amount, i, all) => amount > 0 && all.indexOf(amount) === i && (i === 0 || amount >= total))
                                        .map((amount) => (
                                            <Button key={amount} type="button" size="sm" variant="secondary" onClick={() => setCash(String(amount))}>
                                                {amount === total ? 'Exact' : money(amount)}
                                            </Button>
                                        ))}
                                </div>
                                <InputError message={errors.cash_tendered} />
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Change</span>
                                    <span className="font-semibold tabular-nums">{money(change)}</span>
                                </div>
                            </div>
                        ) : (
                            <div className="grid gap-1.5">
                                <Label htmlFor="reference">Reference no. (optional)</Label>
                                <Input
                                    id="reference"
                                    value={reference}
                                    onChange={(event) => setReference(event.target.value)}
                                    placeholder="GCash / Maya / card ref"
                                />
                            </div>
                        )}

                        <div className="grid gap-1.5">
                            <Label htmlFor="customer">Customer name (optional)</Label>
                            <Input id="customer" value={customer} onChange={(event) => setCustomer(event.target.value)} placeholder="Walk-in guest" />
                        </div>

                        <Button size="lg" disabled={!canCharge} onClick={charge}>
                            {processing && <Loader2 className="animate-spin" />} Charge {money(total)}
                        </Button>
                    </CardContent>
                </Card>
            </div>

            {lines.length > 0 && (
                // Phones: the sale card sits below the whole product grid, so keep a jump-to-sale bar in reach.
                <div className="fixed inset-x-0 bottom-0 z-30 border-t bg-background/95 p-3 backdrop-blur lg:hidden">
                    <Button size="lg" className="w-full" onClick={() => saleRef.current?.scrollIntoView({ behavior: 'smooth' })}>
                        <ShoppingCart /> Review sale · {lines.reduce((sum, line) => sum + line.qty, 0)} items · {money(total)}
                    </Button>
                </div>
            )}

            <Dialog open={receipt !== null} onOpenChange={(open) => !open && setReceipt(null)}>
                <DialogContent className="sm:max-w-sm">
                    {receipt && (
                        <>
                            <DialogHeader>
                                <DialogTitle>Sale complete</DialogTitle>
                                <DialogDescription>{receipt.order_number}</DialogDescription>
                            </DialogHeader>
                            <ul className="grid grid-cols-[minmax(0,1fr)] gap-1 text-sm">
                                {receipt.items.map((item, i) => (
                                    <li key={i} className="flex justify-between gap-2">
                                        <span className="min-w-0 truncate">
                                            {item.qty} × {item.name}
                                        </span>
                                        <span className="shrink-0 tabular-nums">{money(item.subtotal)}</span>
                                    </li>
                                ))}
                            </ul>
                            <div className="grid gap-1 border-t pt-3 text-sm">
                                <div className="flex justify-between font-semibold">
                                    <span>Total</span>
                                    <span className="tabular-nums">{money(receipt.total)}</span>
                                </div>
                                {receipt.cash_tendered !== null && (
                                    <>
                                        <div className="flex justify-between text-muted-foreground">
                                            <span>Cash</span>
                                            <span className="tabular-nums">{money(receipt.cash_tendered)}</span>
                                        </div>
                                        <div className="flex justify-between text-base font-bold">
                                            <span>Change</span>
                                            <span className="tabular-nums">{money(receipt.change_due)}</span>
                                        </div>
                                    </>
                                )}
                            </div>
                            <DialogFooter>
                                <Button variant="outline" asChild>
                                    <a href={invoice(receipt.id, { query: { paper: 'pos', print: 1 } }).url} target="_blank" rel="noreferrer">
                                        <Printer /> Print receipt
                                    </a>
                                </Button>
                                <Button onClick={() => setReceipt(null)}>New sale</Button>
                            </DialogFooter>
                        </>
                    )}
                </DialogContent>
            </Dialog>
        </>
    );
}

Pos.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title: 'POS register' }] };
