import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/orders';
import type { OrderRecord, ColumnDef } from '@/types';

export function createOrderGridSchema(): ColumnDef<OrderRecord>[] {
    return [
        {
            key: 'order_number',
            header: 'Order',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.order_number}</span>
                    {row.customer_name && <span className="truncate text-xs text-muted-foreground">{row.customer_name}</span>}
                </Link>
            ),
        },
        {
            key: 'fulfillment_type',
            header: 'Type',
            sortable: true,
            cell: (row) => (
                <StatusBadge label={row.fulfillment_type ?? (row.fulfillment_type === null ? null : String(row.fulfillment_type))} tone="info" />
            ),
            priorityLevel: 2,
        },
        {
            key: 'payment_status',
            header: 'Payment',
            sortable: true,
            cell: (row) => <StatusBadge label={row.payment_status_label ?? row.payment_status} tone={toneFor(String(row.payment_status))} />,
        },
        { key: 'total', header: 'Total', sortable: true, cell: (row) => <Money value={row.total} />, align: 'right' },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        { key: 'created_at', header: 'Placed', sortable: true, cell: (row) => <DateFormatted value={row.created_at} withTime />, priorityLevel: 2 },
    ];
}
