import { BooleanBadge } from '@/renderers/cells/badges';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { ProductPermission } from '@/lib/permissions';
import { StatusSwitch } from '@/renderers/cells/status-switch';
import { cn } from '@/lib/utils';
import { edit, toggle } from '@/routes/admin/products';
import { formatNumber } from '@/lib/format';
import type { ProductRecord, ColumnDef } from '@/types';

export function createProductGridSchema(): ColumnDef<ProductRecord>[] {
    return [
        {
            key: 'name',
            header: 'Product',
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
            key: 'category_name',
            header: 'Category',
            sortable: true,
            cell: (row) => row.category_name ?? '—',
            priorityLevel: 2,
            sortField: 'category.name',
        },
        { key: 'price', header: 'Price', sortable: true, cell: (row) => <Money value={row.price} />, align: 'right' },
        { key: 'sale_price', header: 'Sale', sortable: true, cell: (row) => <Money value={row.sale_price} />, priorityLevel: 3, align: 'right' },
        {
            key: 'stock_qty',
            header: 'Stock',
            sortable: true,
            cell: (row) => <span className={cn(row.is_low_stock && 'font-semibold text-destructive')}>{formatNumber(row.stock_qty)}</span>,
            align: 'right',
        },
        {
            key: 'is_featured',
            header: 'Featured',
            sortable: true,
            cell: (row) => <BooleanBadge value={row.is_featured} yes="Yes" no="No" />,
            priorityLevel: 3,
            align: 'center',
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
                    permission={ProductPermission.ProductsEdit}
                    label={`Toggle ${row.name}`}
                />
            ),
            align: 'center',
        },
    ];
}
