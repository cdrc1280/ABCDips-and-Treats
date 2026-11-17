import { CountChip } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { edit } from '@/routes/admin/suppliers';
import type { SupplierRecord, ColumnDef } from '@/types';

export function createSupplierGridSchema(): ColumnDef<SupplierRecord>[] {
    return [
        {
            key: 'name',
            header: 'Supplier',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.name}</span>
                    {row.contact_person && <span className="truncate text-xs text-muted-foreground">{row.contact_person}</span>}
                </Link>
            ),
        },
        { key: 'email', header: 'Email', sortable: true, cell: (row) => row.email ?? '—', priorityLevel: 2 },
        { key: 'phone', header: 'Phone', sortable: true, cell: (row) => row.phone ?? '—', priorityLevel: 2 },
        { key: 'payment_terms', header: 'Terms', sortable: true, cell: (row) => row.payment_terms ?? '—', priorityLevel: 3 },
        {
            key: 'ingredients_count',
            header: 'Ingredients',
            sortable: true,
            cell: (row) => <CountChip value={row.ingredients_count} />,
            align: 'right',
        },
        {
            key: 'purchase_orders_count',
            header: 'POs',
            sortable: true,
            cell: (row) => <CountChip value={row.purchase_orders_count} />,
            priorityLevel: 3,
            align: 'right',
        },
        { key: 'created_at', header: 'Added', sortable: true, cell: (row) => <DateFormatted value={row.created_at} />, priorityLevel: 3 },
    ];
}
