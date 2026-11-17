import { CountChip } from '@/renderers/cells/badges';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { cn } from '@/lib/utils';
import { edit } from '@/routes/admin/ingredients';
import { formatNumber } from '@/lib/format';
import type { IngredientRecord, ColumnDef } from '@/types';

export function createIngredientGridSchema(): ColumnDef<IngredientRecord>[] {
    return [
        {
            key: 'name',
            header: 'Ingredient',
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
            key: 'min_stock_qty',
            header: 'Minimum',
            sortable: true,
            cell: (row) => `${formatNumber(row.min_stock_qty)} ${row.unit ?? ''}`,
            priorityLevel: 3,
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
            key: 'supplier_name',
            header: 'Supplier',
            sortable: true,
            cell: (row) => row.supplier_name ?? '—',
            priorityLevel: 2,
            sortField: 'supplier.name',
        },
        { key: 'used_in_recipes', header: 'Recipes', cell: (row) => <CountChip value={row.used_in_recipes} />, priorityLevel: 3, align: 'right' },
    ];
}
