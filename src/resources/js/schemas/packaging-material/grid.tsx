import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { PackagingMaterialPermission } from '@/lib/permissions';
import { StatusBadge } from '@/renderers/cells/badges';
import { StatusSwitch } from '@/renderers/cells/status-switch';
import { cn } from '@/lib/utils';
import { edit, toggle } from '@/routes/admin/packaging-materials';
import { formatNumber } from '@/lib/format';
import type { PackagingMaterialRecord, ColumnDef } from '@/types';

export function createPackagingMaterialGridSchema(): ColumnDef<PackagingMaterialRecord>[] {
    return [
        {
            key: 'name',
            header: 'Item',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.name}</span>
                    {row.sku && <span className="truncate text-xs text-muted-foreground">{row.sku}</span>}
                </Link>
            ),
        },
        {
            key: 'type',
            header: 'Type',
            sortable: true,
            cell: (row) => <StatusBadge label={row.type_label ?? (row.type === null ? null : String(row.type))} tone="info" />,
        },
        {
            key: 'stock_qty',
            header: 'In stock',
            sortable: true,
            cell: (row) => (
                <span className={cn(row.is_low_stock && 'font-semibold text-destructive')}>
                    {formatNumber(row.stock_qty)} {row.unit}
                </span>
            ),
            align: 'right',
        },
        {
            key: 'cost_per_unit',
            header: 'Cost / unit',
            sortable: true,
            cell: (row) => <Money value={row.cost_per_unit} />,
            priorityLevel: 2,
            align: 'right',
        },
        {
            key: 'is_active',
            header: 'Active',
            sortable: true,
            cell: (row) => (
                <StatusSwitch
                    checked={row.is_active}
                    field="is_active"
                    url={toggle(row.id).url}
                    permission={PackagingMaterialPermission.PackagingMaterialsEdit}
                    label={`Toggle ${row.name}`}
                />
            ),
            align: 'center',
        },
    ];
}
