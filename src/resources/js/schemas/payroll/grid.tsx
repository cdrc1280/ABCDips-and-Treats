import { CountChip, StatusBadge, toneFor } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { edit } from '@/routes/admin/payrolls';
import type { PayrollRecord, ColumnDef } from '@/types';

export function createPayrollGridSchema(): ColumnDef<PayrollRecord>[] {
    return [
        {
            key: 'payroll_number',
            header: 'Payroll',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.payroll_number}</span>
                    {row.status && <span className="truncate text-xs text-muted-foreground">{row.status}</span>}
                </Link>
            ),
        },
        { key: 'period_start', header: 'From', sortable: true, cell: (row) => <DateFormatted value={row.period_start} /> },
        { key: 'period_end', header: 'To', sortable: true, cell: (row) => <DateFormatted value={row.period_end} /> },
        {
            key: 'items_count',
            header: 'Staff',
            sortable: true,
            cell: (row) => <CountChip value={row.items_count} />,
            priorityLevel: 2,
            align: 'right',
        },
        { key: 'total_net', header: 'Net pay', sortable: true, cell: (row) => <Money value={row.total_net} />, align: 'right' },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
    ];
}
