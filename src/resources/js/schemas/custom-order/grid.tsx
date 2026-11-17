import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/custom-orders';
import { formatNumber } from '@/lib/format';
import type { CustomOrderRecord, ColumnDef } from '@/types';

export function createCustomOrderGridSchema(): ColumnDef<CustomOrderRecord>[] {
    return [
        {
            key: 'reference_number',
            header: 'Order',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.reference_number}</span>
                    {row.customer_name && <span className="truncate text-xs text-muted-foreground">{row.customer_name}</span>}
                </Link>
            ),
        },
        { key: 'event_date', header: 'Event', sortable: true, cell: (row) => <DateFormatted value={row.event_date} /> },
        {
            key: 'servings_count',
            header: 'Servings',
            sortable: true,
            cell: (row) => `${formatNumber(row.servings_count)}`,
            priorityLevel: 2,
            align: 'right',
        },
        { key: 'quoted_price', header: 'Quote', sortable: true, cell: (row) => <Money value={row.quoted_price} />, priorityLevel: 2, align: 'right' },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
    ];
}
