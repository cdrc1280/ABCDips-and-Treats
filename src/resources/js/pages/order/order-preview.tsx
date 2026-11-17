import { ShoppingBag } from 'lucide-react';

import { Card, CardContent } from '@/components/ui/card';

import type { OrderFormData } from './order-form';

/** Sticky aside: live summary of the record being built. */
export function OrderPreview({ data, title }: { data: OrderFormData; title?: string }) {
    return (
        <aside className="lg:sticky lg:top-20">
            <Card>
                <CardContent className="grid gap-3">
                    <div className="flex items-start gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                            <ShoppingBag className="size-5" />
                        </span>
                        <div className="grid min-w-0 flex-1 gap-1">
                            <p className="truncate font-semibold">{title ?? 'New order'}</p>
                        </div>
                    </div>
                    {data.customer_name && <p className="text-sm text-muted-foreground">{String(data.customer_name)}</p>}
                    <div className="flex justify-between gap-2 text-sm">
                        <span className="text-muted-foreground">Customer</span>
                        <span className="font-medium tabular-nums">{data.customer_email}</span>
                    </div>
                    <div className="flex justify-between gap-2 text-sm">
                        <span className="text-muted-foreground">Payment</span>
                        <span className="font-medium tabular-nums">{data.payment_status}</span>
                    </div>
                </CardContent>
            </Card>
        </aside>
    );
}
