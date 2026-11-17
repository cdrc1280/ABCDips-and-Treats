import { Link } from '@inertiajs/react';
import { Download, FileText, MapPin, Store } from 'lucide-react';

import { CartLineDetails } from '@/components/storefront/cart-line-details';
import { OrderProgress, OrderStatusBadge, OrderTimeline } from '@/components/storefront/order-status';
import { formatDate, money, titleCase } from '@/lib/format';
import type { CustomerOrderDetail } from '@/types';
import { invoice as invoiceRoute } from '@/routes/order';

function Row({ label, value, strong }: { label: string; value: string; strong?: boolean }) {
    return (
        <div
            className={
                strong
                    ? 'flex justify-between border-t border-primary/20 pt-2 text-base font-semibold text-primary'
                    : 'flex justify-between text-sm text-muted-foreground'
            }
        >
            <span>{label}</span>
            <span className="font-mono font-bold text-foreground tabular-nums">{value}</span>
        </div>
    );
}

/** Items, totals, fulfilment and invoice links — shared by account, tracking and confirmation pages. */
export function OrderDetails({ order }: { order: CustomerOrderDetail }) {
    const invoice = invoiceRoute(order.id, { query: { token: order.tracking_token } }).url;
    const printInvoice = invoiceRoute(order.id, { query: { token: order.tracking_token, print: 1 } }).url;

    return (
        <div className="space-y-6">
            <div className="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-primary/20 bg-card p-5">
                <div>
                    <p className="text-xs text-muted-foreground">Order</p>
                    <p className="font-mono text-lg font-semibold text-foreground">#{order.order_number}</p>
                    <p className="text-xs text-muted-foreground">{formatDate(order.created_at, true)}</p>
                </div>
                <OrderStatusBadge status={order.status} label={order.status_label} />
            </div>

            <div className="rounded-2xl border border-primary/20 bg-card p-5">
                <OrderProgress order={order} />
            </div>

            <div className="grid gap-6 lg:grid-cols-3">
                <div className="space-y-4 rounded-2xl border border-primary/20 bg-card p-5 lg:col-span-2">
                    <h2 className="font-bold text-foreground">Items</h2>
                    <ul className="divide-y divide-primary/15">
                        {order.items.map((item) => (
                            <li key={item.id} className="flex gap-3 py-3">
                                <img
                                    src={item.image_url ?? '/images/logo.png'}
                                    alt=""
                                    className="size-14 rounded-xl border border-primary/20 object-cover"
                                />
                                <div className="min-w-0 flex-1">
                                    {item.product_slug ? (
                                        <Link href={`/products/${item.product_slug}`} className="text-sm font-bold text-foreground hover:underline">
                                            {item.product_name}
                                        </Link>
                                    ) : (
                                        <p className="text-sm font-bold text-foreground">{item.product_name}</p>
                                    )}
                                    <CartLineDetails
                                        line={{ ...item, id: item.id, product_id: '', name: item.product_name, slug: item.product_slug }}
                                    />
                                    <p className="font-mono text-xs text-muted-foreground">
                                        {item.qty} × {money(item.unit_price)}
                                    </p>
                                </div>
                                <span className="font-mono text-sm font-semibold text-primary">{money(item.subtotal)}</span>
                            </li>
                        ))}
                    </ul>
                    <div className="space-y-1.5">
                        <Row label="Subtotal" value={money(order.subtotal)} />
                        {order.discount_amount > 0 && (
                            <Row label={`Discount${order.coupon_code ? ` (${order.coupon_code})` : ''}`} value={`-${money(order.discount_amount)}`} />
                        )}
                        {order.delivery_fee > 0 && <Row label="Delivery fee" value={money(order.delivery_fee)} />}
                        {order.delivery_mode === 'pooling' && order.estimated_shared_fee > 0 && (
                            <Row label="Est. shared delivery fee" value={money(order.estimated_shared_fee)} />
                        )}
                        <Row label="Total" value={money(order.total)} strong />
                    </div>
                </div>

                <div className="space-y-6">
                    <div className="space-y-2 rounded-2xl border border-primary/20 bg-card p-5 text-sm">
                        <h2 className="flex items-center gap-2 font-bold text-foreground">
                            {order.fulfillment_type === 'pickup' ? (
                                <Store className="size-4 text-primary" />
                            ) : (
                                <MapPin className="size-4 text-primary" />
                            )}
                            {order.fulfillment_type === 'pickup' ? 'Store pickup' : 'Delivery'}
                        </h2>
                        <p className="font-semibold text-foreground">{order.customer_name}</p>
                        {order.customer_phone && <p className="text-muted-foreground">{order.customer_phone}</p>}
                        {order.delivery_address && <p className="text-muted-foreground">{order.delivery_address}</p>}
                        {order.scheduled_time && <p className="text-muted-foreground">Scheduled: {formatDate(order.scheduled_time, true)}</p>}
                        {order.delivery_mode === 'pooling' && (
                            <p className="text-xs text-muted-foreground">Group delivery: {titleCase(order.pooling_status)}</p>
                        )}
                        <p className="pt-2 text-muted-foreground">
                            Payment: <strong className="text-foreground">{titleCase(order.payment_method)}</strong> ·{' '}
                            {titleCase(order.payment_status)}
                        </p>
                        {order.notes && <p className="text-muted-foreground italic">“{order.notes}”</p>}
                    </div>
                    {order.invoice_available ? (
                        <div className="flex flex-col gap-2">
                            <a
                                href={invoice}
                                target="_blank"
                                rel="noreferrer"
                                className="flex items-center justify-center gap-2 rounded-xl border border-primary/30 px-4 py-2.5 text-sm font-bold text-primary hover:bg-highlight/20"
                            >
                                <FileText className="size-4" /> View invoice
                            </a>
                            <a
                                href={printInvoice}
                                target="_blank"
                                rel="noreferrer"
                                className="flex items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-primary-foreground hover:bg-primary/90"
                            >
                                <Download className="size-4" /> Download PDF
                            </a>
                        </div>
                    ) : (
                        <p className="text-xs text-muted-foreground">The invoice is available once the group delivery is settled.</p>
                    )}
                    <div className="rounded-2xl border border-primary/20 bg-card p-5">
                        <h2 className="mb-3 font-bold text-foreground">History</h2>
                        <OrderTimeline order={order} />
                    </div>
                </div>
            </div>
        </div>
    );
}
