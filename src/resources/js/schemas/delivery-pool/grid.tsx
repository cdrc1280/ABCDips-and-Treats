import { CountChip, StatusBadge, toneFor } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { edit } from '@/routes/admin/delivery-pools';
import type { DeliveryPoolRecord, ColumnDef } from '@/types';

export function createDeliveryPoolGridSchema(): ColumnDef<DeliveryPoolRecord>[] {
    return [
        {
            key: 'pool_code',
            header: 'Pool',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.pool_code}</span>
                    {row.zone_name && <span className="truncate text-xs text-muted-foreground">{row.zone_name}</span>}
                </Link>
            ),
        },
        { key: 'city', header: 'City', sortable: true, cell: (row) => row.city ?? '—', priorityLevel: 2 },
        { key: 'orders_count', header: 'Orders', sortable: true, cell: (row) => <CountChip value={row.orders_count} />, align: 'right' },
        { key: 'total_delivery_fee', header: 'Trip fee', sortable: true, cell: (row) => <Money value={row.total_delivery_fee} />, align: 'right' },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        { key: 'created_at', header: 'Created', sortable: true, cell: (row) => <DateFormatted value={row.created_at} />, priorityLevel: 3 },
    ];
}
