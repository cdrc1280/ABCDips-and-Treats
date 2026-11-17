import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/production-batches';
import { formatNumber } from '@/lib/format';
import type { ProductionBatchRecord, ColumnDef } from '@/types';

export function createProductionBatchGridSchema(): ColumnDef<ProductionBatchRecord>[] {
    return [
        {
            key: 'batch_number',
            header: 'Batch',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.batch_number}</span>
                    {row.product_name && <span className="truncate text-xs text-muted-foreground">{row.product_name}</span>}
                </Link>
            ),
        },
        { key: 'recipe_name', header: 'Recipe', cell: (row) => row.recipe_name ?? '—', priorityLevel: 2 },
        { key: 'planned_qty', header: 'Planned', sortable: true, cell: (row) => `${formatNumber(row.planned_qty)}`, align: 'right' },
        {
            key: 'actual_yield_qty',
            header: 'Yield',
            sortable: true,
            cell: (row) => `${formatNumber(row.actual_yield_qty)}`,
            priorityLevel: 2,
            align: 'right',
        },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        {
            key: 'completed_at',
            header: 'Completed',
            sortable: true,
            cell: (row) => <DateFormatted value={row.completed_at} withTime />,
            priorityLevel: 3,
        },
    ];
}
