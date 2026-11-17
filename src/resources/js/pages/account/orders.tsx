import { Head, InfiniteScroll, Link } from '@inertiajs/react';
import { Cake, ChevronRight, Package } from 'lucide-react';

import { AccountShell } from '@/components/storefront/account-shell';
import { OrderStatusBadge } from '@/components/storefront/order-status';
import { EmptyState } from '@/components/storefront/page-header';
import { Button } from '@/components/ui/button';
import { formatDate, money, titleCase } from '@/lib/format';
import type { CustomerOrderSummary } from '@/types';

interface Props {
    orders: { data: CustomerOrderSummary[] };
    customOrders: {
        id: string;
        reference: string;
        status: string;
        event_date: string | null;
        quoted_price: number | null;
        created_at: string | null;
    }[];
}

export default function Orders({ orders, customOrders }: Props) {
    return (
        <AccountShell>
            <Head title="My Orders" />
            <h1 className="mb-6 font-display text-2xl text-foreground">My Orders</h1>
            {orders.data.length === 0 ? (
                <EmptyState
                    icon={<Package className="size-7" />}
                    title="No orders yet"
                    description="Your orders will appear here once you check out."
                    action={
                        <Button asChild>
                            <Link href="/shop">Start shopping</Link>
                        </Button>
                    }
                />
            ) : (
                <InfiniteScroll data="orders" className="space-y-3">
                    {orders.data.map((order) => (
                        <Link
                            key={order.id}
                            href={`/account/orders/${order.id}`}
                            className="flex items-center justify-between gap-4 rounded-2xl border border-primary/20 bg-card p-4 transition-all hover:border-primary/50 hover:shadow-md"
                        >
                            <div className="min-w-0">
                                <div className="flex flex-wrap items-center gap-2">
                                    <span className="font-mono font-semibold text-foreground">#{order.order_number}</span>
                                    <OrderStatusBadge status={order.status} label={order.status_label} />
                                </div>
                                <p className="mt-1 text-xs text-muted-foreground">
                                    {formatDate(order.created_at)} · {order.items_count} items · {titleCase(order.payment_method)}
                                </p>
                            </div>
                            <div className="flex items-center gap-3">
                                <span className="font-mono font-semibold text-primary">{money(order.total)}</span>
                                <ChevronRight className="size-4 text-muted-foreground" />
                            </div>
                        </Link>
                    ))}
                </InfiniteScroll>
            )}

            {customOrders.length > 0 && (
                <section className="mt-12">
                    <h2 className="mb-4 flex items-center gap-2 text-xl font-semibold text-foreground">
                        <Cake className="size-5 text-primary" /> Custom cake requests
                    </h2>
                    <div className="grid gap-3 sm:grid-cols-2">
                        {customOrders.map((request) => (
                            <div key={request.id} className="rounded-2xl border border-primary/20 bg-card p-4">
                                <div className="flex items-center justify-between">
                                    <span className="font-mono font-bold text-foreground">{request.reference}</span>
                                    <OrderStatusBadge status={request.status} label={titleCase(request.status)} />
                                </div>
                                <p className="mt-1 text-xs text-muted-foreground">
                                    Event {formatDate(request.event_date)} ·{' '}
                                    {request.quoted_price !== null ? `Quote ${money(request.quoted_price)}` : 'Awaiting quote'}
                                </p>
                            </div>
                        ))}
                    </div>
                </section>
            )}
        </AccountShell>
    );
}
