import { Badge } from '@/components/ui/badge';
import { titleCase } from '@/lib/format';
import { cn } from '@/lib/utils';

export type Tone = 'success' | 'warning' | 'neutral' | 'danger' | 'info';

/** Status chip in the shared tonal colours (Badge success/warning/danger/info/neutral). */
export function StatusBadge({ label, tone = 'neutral', className }: { label: string | null | undefined; tone?: Tone; className?: string }) {
    if (!label) return <span className="text-muted-foreground">—</span>;
    return (
        <Badge variant={tone} className={cn('font-medium', className)}>
            {titleCase(label)}
        </Badge>
    );
}

export function BooleanBadge({ value, yes = 'Active', no = 'Inactive' }: { value: boolean; yes?: string; no?: string }) {
    return <StatusBadge label={value ? yes : no} tone={value ? 'success' : 'neutral'} />;
}

/** Small count chip (e.g. "12 items"). */
export function CountChip({ value, label }: { value: number; label?: string }) {
    return (
        <span className="inline-flex items-center rounded-sm border bg-muted/50 px-1.5 py-0.5 text-xs tabular-nums">
            {value.toLocaleString('en-US')}
            {label ? ` ${label}` : ''}
        </span>
    );
}

/** Maps common status strings to tones, so modules don't each reinvent it. */
export function toneFor(status: string | null | undefined): Tone {
    switch (status) {
        case 'completed':
        case 'paid':
        case 'approved':
        case 'received':
        case 'delivered':
        case 'active':
        case 'resolved':
        case 'in_stock':
        case 'confirmed':
            return 'success';
        case 'pending':
        case 'draft':
        case 'processing':
        case 'preparing':
        case 'in_progress':
        case 'awaiting_payment':
        case 'low_stock':
        case 'open':
        case 'ordered':
        case 'sent':
            return 'warning';
        case 'cancelled':
        case 'canceled':
        case 'failed':
        case 'rejected':
        case 'refunded':
        case 'out_of_stock':
        case 'expired':
            return 'danger';
        case 'ready':
        case 'out_for_delivery':
        case 'shipped':
            return 'info';
        default:
            return 'neutral';
    }
}
