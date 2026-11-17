import { Head, useForm } from '@inertiajs/react';
import { PackageSearch } from 'lucide-react';

import { OrderDetails } from '@/components/storefront/order-details';
import { PageHeader } from '@/components/storefront/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { CustomerOrderDetail } from '@/types';

/** Live order tracking. Without a token, asks for the order number + email it was placed with. */
export default function Track({ order }: { order: CustomerOrderDetail | null }) {
    const form = useForm({ order_number: '', email: '' });

    if (order) {
        return (
            <>
                <Head title={`Tracking #${order.order_number}`} />
                <div className="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
                    <OrderDetails order={order} />
                </div>
            </>
        );
    }

    return (
        <>
            <Head title="Track your order" />
            <div className="mx-auto max-w-md px-4 pb-20">
                <PageHeader title="Track your order" subtitle="Enter the order number from your confirmation email and the email you ordered with." />
                <form
                    className="ticket-notch space-y-4 rounded-2xl bg-card p-6 shadow-md [--notch-y:calc(100%-5.25rem)]"
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post('/track');
                    }}
                >
                    <div className="space-y-1.5">
                        <Label htmlFor="order_number">Order number</Label>
                        <Input
                            id="order_number"
                            value={form.data.order_number}
                            onChange={(e) => form.setData('order_number', e.target.value)}
                            required
                            placeholder="e.g. ORD-20261115-0001"
                        />
                        {form.errors.order_number && <p className="text-xs text-destructive">{form.errors.order_number}</p>}
                    </div>
                    <div className="space-y-1.5">
                        <Label htmlFor="email">Email</Label>
                        <Input id="email" type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} required />
                    </div>
                    <hr aria-hidden className="-mx-6 !mt-6 border-t-2 border-dashed border-border" />
                    <Button type="submit" className="w-full" disabled={form.processing}>
                        <PackageSearch className="size-4" /> Find my order
                    </Button>
                </form>
            </div>
        </>
    );
}
