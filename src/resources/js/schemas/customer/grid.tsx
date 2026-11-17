import { BooleanBadge, CountChip } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { edit } from '@/routes/admin/customers';
import type { CustomerRecord, ColumnDef } from '@/types';

export function createCustomerGridSchema(): ColumnDef<CustomerRecord>[] {
    return [
        {
            key: 'name',
            header: 'Customer',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.name}</span>
                    {row.email && <span className="truncate text-xs text-muted-foreground">{row.email}</span>}
                </Link>
            ),
        },
        { key: 'city', header: 'City', sortable: true, cell: (row) => row.city ?? '—', priorityLevel: 2 },
        { key: 'orders_count', header: 'Orders', sortable: true, cell: (row) => <CountChip value={row.orders_count} />, align: 'right' },
        { key: 'total_spent', header: 'Spent', cell: (row) => <Money value={row.total_spent} />, align: 'right' },
        {
            key: 'email_verified',
            header: 'Verified',
            cell: (row) => <BooleanBadge value={row.email_verified} yes="Yes" no="No" />,
            priorityLevel: 3,
            align: 'center',
        },
        { key: 'created_at', header: 'Joined', sortable: true, cell: (row) => <DateFormatted value={row.created_at} />, priorityLevel: 2 },
    ];
}
