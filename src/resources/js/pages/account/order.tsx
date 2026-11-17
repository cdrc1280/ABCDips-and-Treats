import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useState } from 'react';

import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { AccountShell } from '@/components/storefront/account-shell';
import { OrderDetails } from '@/components/storefront/order-details';
import { Button } from '@/components/ui/button';
import type { CustomerOrderDetail } from '@/types';

export default function AccountOrder({ order }: { order: CustomerOrderDetail }) {
    const [confirming, setConfirming] = useState(false);
    const [processing, setProcessing] = useState(false);

    return (
        <AccountShell>
            <Head title={`Order #${order.order_number}`} />
            <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                <Link href="/account/orders" className="flex items-center gap-1.5 text-sm font-bold text-primary hover:underline">
                    <ArrowLeft className="size-4" /> All orders
                </Link>
                {order.can_cancel && (
                    <Button variant="destructive" onClick={() => setConfirming(true)}>
                        Cancel order
                    </Button>
                )}
            </div>
            <OrderDetails order={order} />
            <ConfirmDialog
                open={confirming}
                onOpenChange={setConfirming}
                title="Cancel this order?"
                description="We’ll stop preparing it and release the items. This can’t be undone."
                confirmLabel="Cancel order"
                processing={processing}
                onConfirm={() =>
                    router.post(
                        `/account/orders/${order.id}/cancel`,
                        {},
                        {
                            preserveScroll: true,
                            onStart: () => setProcessing(true),
                            onFinish: () => {
                                setProcessing(false);
                                setConfirming(false);
                            },
                        },
                    )
                }
            />
        </AccountShell>
    );
}
