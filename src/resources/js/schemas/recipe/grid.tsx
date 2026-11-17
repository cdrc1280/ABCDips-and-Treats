import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { edit } from '@/routes/admin/recipes';
import { formatNumber } from '@/lib/format';
import type { RecipeRecord, ColumnDef } from '@/types';

export function createRecipeGridSchema(): ColumnDef<RecipeRecord>[] {
    return [
        {
            key: 'name',
            header: 'Recipe',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.name}</span>
                    {row.product_name && <span className="truncate text-xs text-muted-foreground">{row.product_name}</span>}
                </Link>
            ),
        },
        { key: 'yield_qty', header: 'Yield', sortable: true, cell: (row) => `${formatNumber(row.yield_qty)}`, priorityLevel: 2, align: 'right' },
        { key: 'unit_cost', header: 'Unit cost', cell: (row) => <Money value={row.unit_cost} />, align: 'right' },
        { key: 'unit_selling_price', header: 'Suggested price', cell: (row) => <Money value={row.unit_selling_price} />, align: 'right' },
        { key: 'product_price', header: 'Current price', cell: (row) => <Money value={row.product_price} />, priorityLevel: 2, align: 'right' },
    ];
}
