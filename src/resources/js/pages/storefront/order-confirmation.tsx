import { Head, Link, usePage } from '@inertiajs/react';
import { CheckCircle2, Info, Users } from 'lucide-react';

import { OrderDetails } from '@/components/storefront/order-details';
import { Button } from '@/components/ui/button';
import { money } from '@/lib/format';
import type { CustomerOrderDetail } from '@/types';

interface Props {
    order: CustomerOrderDetail;
    bank: { name: string; account_name: string; account_number: string; instructions: string };
}

function Notice({ icon: Icon, title, children, tone }: { icon: typeof Info; title: string; children: React.ReactNode; tone: string }) {
    return (
        <div className={`flex gap-3 rounded-2xl border p-4 text-sm ${tone}`}>
            <Icon className="mt-0.5 size-5 shrink-0" />
            <div>
                <p className="font-bold">{title}</p>
                <div className="mt-0.5">{children}</div>
            </div>
        </div>
    );
}

export default function OrderConfirmation({ order, bank }: Props) {
    const user = usePage().props.auth.user;
    const paid = order.payment_status === 'paid';

    return (
        <>
            <Head title={`Order #${order.order_number} placed`} />
            <div className="mx-auto max-w-6xl space-y-6 px-4 py-10 sm:px-6 lg:px-8">
                <div className="animate-in rounded-3xl border border-success/30 bg-success/10 p-8 text-center duration-500 zoom-in-95 fade-in">
                    <CheckCircle2 className="mx-auto size-14 text-success" />
                    <h1 className="mt-3 font-display text-2xl text-foreground sm:text-3xl">Thank you! Your order is in.</h1>
                    <p className="mt-1 text-sm text-muted-foreground">
                        Order <strong className="font-mono text-foreground">#{order.order_number}</strong> · a confirmation was sent to{' '}
                        {order.customer_email}
                    </p>
                    <div className="mt-6 flex flex-wrap justify-center gap-3">
                        <Button asChild size="lg">
                            <Link href={`/track/${order.tracking_token}`}>Track order progress →</Link>
                        </Button>
                        {user && (
                            <Button asChild size="lg" variant="outline">
                                <Link href="/account/orders">View order history</Link>
                            </Button>
                        )}
                    </div>
                </div>

                {order.delivery_mode === 'pooling' ? (
                    <Notice icon={Users} title="Group delivery request submitted" tone="border-warning/30 bg-warning/10 text-warning-ink">
                        We’ll batch your order with nearby deliveries in <strong>{order.city}</strong> and assign your shared delivery fee. Your
                        invoice becomes available once the group is settled.
                    </Notice>
                ) : order.payment_method === 'bank_transfer' && !paid ? (
                    <Notice icon={Info} title={`${bank.name} bank transfer`} tone="border-info/30 bg-info/10 text-info-ink">
                        Please transfer <strong>{money(order.total)}</strong> to <strong>{bank.name}</strong>
                        {bank.account_number && (
                            <>
                                {' '}
                                account <strong className="font-mono">{bank.account_number}</strong>
                            </>
                        )}{' '}
                        ({bank.account_name}) and use <strong>#{order.order_number}</strong> as the reference.
                        {bank.instructions && <p className="mt-1">{bank.instructions}</p>}
                    </Notice>
                ) : paid ? (
                    <Notice icon={CheckCircle2} title="Payment received" tone="border-success/30 bg-success/10 text-success-ink">
                        {order.payment_reference ? (
                            <>
                                Reference: <strong className="font-mono">{order.payment_reference}</strong>.{' '}
                            </>
                        ) : null}
                        Your order is queued for baking!
                    </Notice>
                ) : null}

                <OrderDetails order={order} />
            </div>
        </>
    );
}
