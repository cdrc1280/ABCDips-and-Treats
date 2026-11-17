import { Check, ChefHat, Clock, Package, PartyPopper, Truck, XCircle } from 'lucide-react';

import { formatDate } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { CustomerOrderDetail } from '@/types';

const TONES: Record<string, string> = {
    pending: 'bg-warning/10 text-warning-ink',
    confirmed: 'bg-info/10 text-info-ink',
    preparing: 'bg-warning/10 text-warning-ink',
    packaging: 'bg-warning/10 text-warning-ink',
    out_for_delivery: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-200',
    ready_for_pickup: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/40 dark:text-indigo-200',
    completed: 'bg-success/10 text-success-ink',
    cancelled: 'bg-destructive/10 text-destructive-ink',
    refunded: 'bg-muted text-muted-foreground',
};

export function OrderStatusBadge({ status, label }: { status: string; label: string }) {
    return (
        <span
            className={cn(
                'rounded-full px-2.5 py-0.5 text-[11px] font-bold tracking-wide uppercase',
                TONES[status] ?? 'bg-muted text-muted-foreground',
            )}
        >
            {label}
        </span>
    );
}

/** Progress steps from placed to completed; cancelled/refunded orders show a single end state. */
export function OrderProgress({ order }: { order: CustomerOrderDetail }) {
    if (order.status === 'cancelled' || order.status === 'refunded') {
        return (
            <div className="flex items-center gap-3 rounded-2xl border border-destructive/30 bg-destructive/10 p-4 text-sm font-bold text-destructive-ink">
                <XCircle className="size-5" /> This order was {order.status_label.toLowerCase()}.
            </div>
        );
    }

    const pickup = order.fulfillment_type === 'pickup';
    const steps = [
        { key: 'pending', label: 'Placed', icon: Clock },
        { key: 'confirmed', label: 'Confirmed', icon: Check },
        { key: 'preparing', label: 'Baking', icon: ChefHat },
        { key: 'packaging', label: 'Packed', icon: Package },
        { key: pickup ? 'ready_for_pickup' : 'out_for_delivery', label: pickup ? 'Ready' : 'On the way', icon: Truck },
        { key: 'completed', label: 'Enjoy!', icon: PartyPopper },
    ];
    const current = Math.max(
        0,
        steps.findIndex((s) => s.key === order.status),
    );

    return (
        <ol className="grid grid-cols-6 gap-1" aria-label="Order progress">
            {steps.map((step, i) => (
                <li key={step.key} className="flex flex-col items-center gap-1.5 text-center">
                    <span
                        className={cn(
                            'flex size-9 items-center justify-center rounded-full border-2 transition-colors',
                            i <= current ? 'border-primary bg-primary text-primary-foreground' : 'border-primary/30 text-muted-foreground',
                            i === current && 'ring-4 ring-highlight/50',
                        )}
                    >
                        <step.icon className="size-4" />
                    </span>
                    <span className={cn('text-[10px] font-bold sm:text-xs', i <= current ? 'text-primary' : 'text-muted-foreground')}>
                        {step.label}
                    </span>
                </li>
            ))}
        </ol>
    );
}

export function OrderTimeline({ order }: { order: CustomerOrderDetail }) {
    if (!order.timeline.length) return null;
    return (
        <ol className="space-y-3 border-l-2 border-primary/30 pl-4">
            {order.timeline.map((entry, i) => (
                <li key={i} className="relative">
                    <span className="absolute top-1.5 -left-[21px] size-2.5 rounded-full bg-primary" />
                    <p className="text-sm font-bold text-foreground">{entry.label}</p>
                    {entry.comment && <p className="text-xs text-muted-foreground">{entry.comment}</p>}
                    <p className="text-[11px] text-muted-foreground">{formatDate(entry.created_at, true)}</p>
                </li>
            ))}
        </ol>
    );
}
