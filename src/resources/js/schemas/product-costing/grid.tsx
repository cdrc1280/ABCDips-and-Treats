import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { edit } from '@/routes/admin/product-costings';
import type { ProductCostingRecord, ColumnDef } from '@/types';

export function createProductCostingGridSchema(): ColumnDef<ProductCostingRecord>[] {
    return [
        {
            key: 'name',
            header: 'Costing sheet',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.name}</span>
                    {row.yield_unit && <span className="truncate text-xs text-muted-foreground">{row.yield_unit}</span>}
                </Link>
            ),
        },
        { key: 'total_cost', header: 'Total cost', cell: (row) => <Money value={row.total_cost} />, align: 'right' },
        { key: 'selling_price', header: 'Selling price', cell: (row) => <Money value={row.selling_price} />, align: 'right' },
        { key: 'price_per_piece', header: 'Per piece', cell: (row) => <Money value={row.price_per_piece} />, priorityLevel: 2, align: 'right' },
        { key: 'updated_at', header: 'Updated', sortable: true, cell: (row) => <DateFormatted value={row.updated_at} />, priorityLevel: 3 },
    ];
}
