import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/purchase-orders';
import type { PurchaseOrderRecord, ColumnDef } from '@/types';

export function createPurchaseOrderGridSchema(): ColumnDef<PurchaseOrderRecord>[] {
    return [
        {
            key: 'po_number',
            header: 'PO',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.po_number}</span>
                    {row.supplier_name && <span className="truncate text-xs text-muted-foreground">{row.supplier_name}</span>}
                </Link>
            ),
        },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        {
            key: 'expected_delivery_date',
            header: 'Expected',
            sortable: true,
            cell: (row) => <DateFormatted value={row.expected_delivery_date} />,
            priorityLevel: 2,
        },
        { key: 'received_at', header: 'Received', sortable: true, cell: (row) => <DateFormatted value={row.received_at} />, priorityLevel: 3 },
        { key: 'total', header: 'Total', sortable: true, cell: (row) => <Money value={row.total} />, align: 'right' },
    ];
}
