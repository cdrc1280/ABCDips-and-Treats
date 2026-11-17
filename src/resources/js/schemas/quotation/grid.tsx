import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/quotations';
import type { QuotationRecord, ColumnDef } from '@/types';

export function createQuotationGridSchema(): ColumnDef<QuotationRecord>[] {
    return [
        {
            key: 'quotation_number',
            header: 'Quotation',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.quotation_number}</span>
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
        { key: 'quotation_date', header: 'Date', sortable: true, cell: (row) => <DateFormatted value={row.quotation_date} />, priorityLevel: 2 },
        { key: 'valid_until', header: 'Valid until', sortable: true, cell: (row) => <DateFormatted value={row.valid_until} />, priorityLevel: 3 },
        { key: 'total', header: 'Total', sortable: true, cell: (row) => <Money value={row.total} />, align: 'right' },
    ];
}
