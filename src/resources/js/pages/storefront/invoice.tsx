import { Head, Link, usePage } from '@inertiajs/react';
import { FileText, Printer, ReceiptText } from 'lucide-react';
import { useEffect, type ReactNode } from 'react';

import { Button } from '@/components/ui/button';
import { formatDate, money, titleCase } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { SharedData } from '@/types';

interface InvoiceItem {
    name: string;
    detail: string | null;
    qty: number;
    unit_price: number;
    subtotal: number;
}

interface Invoice {
    order_number: string;
    issued_at: string | null;
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    address: string;
    fulfillment: string;
    pool_code: string | null;
    payment_method: string;
    payment_status: string;
    payment_reference: string | null;
    subtotal: number;
    discount: number;
    delivery_fee: number;
    total: number;
    bank: { name: string; number: string };
    items: InvoiceItem[];
}

interface Props {
    invoice: Invoice;
    paper: 'a4' | 'pos';
    autoPrint: boolean;
}

/**
 * Printable invoice (A4) and 80mm thermal receipt. "Save as PDF" is the browser print dialog,
 * so the PDF matches the screen exactly and nothing is rendered server-side.
 */
export default function InvoicePage({ invoice, paper, autoPrint }: Props) {
    const { store } = usePage<SharedData>().props;
    const storeName = store?.name || 'ABCDips & Treats';
    const pos = paper === 'pos';

    useEffect(() => {
        if (!autoPrint) return;
        const timer = window.setTimeout(() => window.print(), 300); // let fonts and the logo paint first
        return () => window.clearTimeout(timer);
    }, [autoPrint]);

    const paperHref = (next: 'a4' | 'pos') => {
        const url = new URL(window.location.href);
        url.searchParams.delete('print');
        url.searchParams.set('paper', next);
        return `${url.pathname}${url.search}`;
    };

    return (
        <>
            <Head title={`${pos ? 'Receipt' : 'Invoice'} ${invoice.order_number}`} />
            <style>{`@page { size: ${pos ? '80mm auto' : 'A4'}; margin: ${pos ? '4mm' : '14mm'}; }`}</style>

            <div className="min-h-svh bg-muted/40 px-4 py-6 print:min-h-0 print:bg-white print:p-0">
                <div
                    className={cn('mx-auto mb-4 flex flex-wrap items-center justify-between gap-2 print:hidden', pos ? 'max-w-[80mm]' : 'max-w-3xl')}
                >
                    <div className="flex rounded-lg border bg-card p-0.5 text-sm">
                        {(['a4', 'pos'] as const).map((option) => (
                            <Link
                                key={option}
                                href={paperHref(option)}
                                preserveScroll
                                className={cn(
                                    'flex items-center gap-1.5 rounded-md px-3 py-1.5 font-medium text-muted-foreground',
                                    paper === option && 'bg-primary text-primary-foreground',
                                )}
                            >
                                {option === 'a4' ? <FileText className="size-4" /> : <ReceiptText className="size-4" />}
                                {option === 'a4' ? 'Invoice' : 'Receipt'}
                            </Link>
                        ))}
                    </div>
                    <Button onClick={() => window.print()}>
                        <Printer /> Print or save as PDF
                    </Button>
                </div>

                {pos ? (
                    <Receipt invoice={invoice} storeName={storeName} storePhone={store?.phone} />
                ) : (
                    <A4 invoice={invoice} storeName={storeName} store={store} />
                )}
            </div>
        </>
    );
}

// Print never uses the dark theme: paper is white, ink is black/cocoa.
const sheet = 'bg-card text-card-foreground print:bg-white print:text-black print:shadow-none print:border-0';

function A4({ invoice, storeName, store }: { invoice: Invoice; storeName: string; store: SharedData['store'] }) {
    return (
        <article className={cn(sheet, 'mx-auto max-w-3xl rounded-2xl border p-6 shadow-sm sm:p-10 print:max-w-none print:rounded-none print:p-0')}>
            <header className="flex flex-wrap items-start justify-between gap-6 border-b pb-6">
                <div className="flex items-center gap-3">
                    <img src="/images/logo.png" alt="" className="h-14 w-auto" />
                    <div>
                        <p className="font-serif text-xl">{storeName}</p>
                        {store?.address && <p className="max-w-xs text-xs text-muted-foreground print:text-neutral-600">{store.address}</p>}
                        <p className="text-xs text-muted-foreground print:text-neutral-600">
                            {[store?.phone, store?.email].filter(Boolean).join(' · ')}
                        </p>
                    </div>
                </div>
                <div className="text-right">
                    <h1 className="font-serif text-3xl text-primary print:text-black">Invoice</h1>
                    <p className="mt-1 font-mono text-sm">#{invoice.order_number}</p>
                    <p className="text-sm text-muted-foreground print:text-neutral-600">{formatDate(invoice.issued_at)}</p>
                </div>
            </header>

            <section className="grid gap-6 py-6 sm:grid-cols-2 print:grid-cols-2">
                <div>
                    <p className="text-xs font-semibold text-muted-foreground uppercase print:text-neutral-600">Billed to</p>
                    <p className="mt-1 font-semibold">{invoice.customer_name}</p>
                    <p className="text-sm">{invoice.customer_email}</p>
                    <p className="text-sm">{invoice.customer_phone}</p>
                </div>
                <div className="sm:text-right print:text-right">
                    <p className="text-xs font-semibold text-muted-foreground uppercase print:text-neutral-600">{titleCase(invoice.fulfillment)}</p>
                    {invoice.address && <p className="mt-1 text-sm">{invoice.address}</p>}
                    {invoice.pool_code && <p className="text-sm">Group delivery · batch #{invoice.pool_code}</p>}
                </div>
            </section>

            <div className="overflow-x-auto">
                <table className="w-full min-w-[28rem] text-sm">
                    <thead>
                        <tr className="border-y text-left text-xs text-muted-foreground uppercase print:text-neutral-600">
                            <th className="py-2 pr-2 font-semibold">Item</th>
                            <th className="w-14 py-2 text-center font-semibold">Qty</th>
                            <th className="w-28 py-2 text-right font-semibold">Price</th>
                            <th className="w-28 py-2 text-right font-semibold">Amount</th>
                        </tr>
                    </thead>
                    <tbody className="[&_td]:py-2.5 [&_tr]:break-inside-avoid [&_tr]:border-b">
                        {invoice.items.map((item, index) => (
                            <tr key={index}>
                                <td className="pr-2">
                                    <p className="font-medium">{item.name}</p>
                                    {item.detail && <p className="text-xs text-muted-foreground print:text-neutral-600">{item.detail}</p>}
                                </td>
                                <td className="text-center tabular-nums">{item.qty}</td>
                                <td className="text-right tabular-nums">{money(item.unit_price)}</td>
                                <td className="text-right font-medium tabular-nums">{money(item.subtotal)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            <section className="mt-4 ml-auto grid max-w-xs gap-1.5 text-sm">
                <Line label="Subtotal" value={money(invoice.subtotal)} />
                {invoice.discount > 0 && <Line label="Discount" value={`−${money(invoice.discount)}`} />}
                {invoice.delivery_fee > 0 && (
                    <Line label={invoice.pool_code ? 'Shared delivery fee' : 'Delivery fee'} value={money(invoice.delivery_fee)} />
                )}
                <div className="mt-1 flex justify-between rounded-lg bg-primary px-3 py-2 text-base font-bold text-primary-foreground print:border print:border-black print:bg-white print:text-black">
                    <span>Total</span>
                    <span className="tabular-nums">{money(invoice.total)}</span>
                </div>
            </section>

            <footer className="mt-8 grid gap-1 rounded-xl border border-dashed p-4 text-sm print:break-inside-avoid">
                <p>
                    <span className="font-semibold">Payment:</span> {titleCase(invoice.payment_method)} · {titleCase(invoice.payment_status)}
                    {invoice.payment_reference && (
                        <span className="text-muted-foreground print:text-neutral-600"> · Ref {invoice.payment_reference}</span>
                    )}
                </p>
                {invoice.bank.number && (
                    <p className="text-xs text-muted-foreground print:text-neutral-600">
                        Bank transfer: BDO {invoice.bank.number} ({invoice.bank.name})
                    </p>
                )}
                <p className="pt-2 text-xs text-muted-foreground print:text-neutral-600">Thank you for ordering from {storeName}.</p>
            </footer>
        </article>
    );
}

/** 80mm thermal layout: monospace, black ink only, no backgrounds. */
function Receipt({ invoice, storeName, storePhone }: { invoice: Invoice; storeName: string; storePhone?: string }) {
    return (
        <article
            className={cn(
                sheet,
                'mx-auto w-full max-w-[80mm] rounded-lg border p-4 font-mono text-[11px] leading-snug shadow-sm print:max-w-none print:rounded-none print:p-0',
            )}
        >
            <header className="text-center">
                <p className="text-sm font-bold uppercase">{storeName}</p>
                {storePhone && <p>{storePhone}</p>}
            </header>
            <Divider double />
            <dl className="grid grid-cols-[auto_minmax(0,1fr)] gap-x-2">
                <dt>Receipt</dt>
                <dd className="text-right">#{invoice.order_number}</dd>
                <dt>Date</dt>
                <dd className="text-right">{formatDate(invoice.issued_at, true)}</dd>
                <dt>Customer</dt>
                <dd className="truncate text-right">{invoice.customer_name}</dd>
                <dt>Type</dt>
                <dd className="text-right">
                    {titleCase(invoice.fulfillment)}
                    {invoice.pool_code && ` · #${invoice.pool_code}`}
                </dd>
            </dl>
            <Divider />
            <ul className="grid gap-1.5">
                {invoice.items.map((item, index) => (
                    <li key={index}>
                        <div className="flex justify-between gap-2 font-bold">
                            <span className="min-w-0">
                                {item.qty} × {item.name}
                            </span>
                            <span className="shrink-0 tabular-nums">{money(item.subtotal)}</span>
                        </div>
                        {item.detail && <p className="opacity-80">{item.detail}</p>}
                        <p className="opacity-80">@ {money(item.unit_price)}</p>
                    </li>
                ))}
                {invoice.delivery_fee > 0 && (
                    <li className="flex justify-between gap-2">
                        <span>{invoice.pool_code ? 'Shared delivery' : 'Delivery fee'}</span>
                        <span className="tabular-nums">{money(invoice.delivery_fee)}</span>
                    </li>
                )}
            </ul>
            <Divider />
            <div className="grid gap-0.5">
                <Line label="Subtotal" value={money(invoice.subtotal)} />
                {invoice.discount > 0 && <Line label="Discount" value={`−${money(invoice.discount)}`} />}
            </div>
            <Divider double />
            <Line label="TOTAL" value={money(invoice.total)} className="text-sm font-bold" />
            <Divider />
            <div className="text-center">
                <p>
                    {invoice.payment_method.toUpperCase()} · {invoice.payment_status.toUpperCase()}
                </p>
                {invoice.payment_reference && <p>Ref {invoice.payment_reference}</p>}
                <p className="mt-2 font-bold">Thank you!</p>
            </div>
        </article>
    );
}

function Line({ label, value, className }: { label: string; value: string; className?: string }) {
    return (
        <div className={cn('flex justify-between gap-2', className)}>
            <span>{label}</span>
            <span className="tabular-nums">{value}</span>
        </div>
    );
}

function Divider({ double }: { double?: boolean }) {
    return <div className={cn('my-2 border-current/40', double ? 'border-t-4 border-double' : 'border-t border-dashed')} />;
}

InvoicePage.layout = (page: ReactNode) => page;
