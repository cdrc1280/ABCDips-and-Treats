import { Link } from '@inertiajs/react';
import { ImageOff } from 'lucide-react';

import { CategoryPermission } from '@/lib/permissions';
import { CountChip } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { StatusSwitch } from '@/renderers/cells/status-switch';
import { edit, toggle } from '@/routes/admin/categories';
import type { CategoryRecord, ColumnDef } from '@/types';

function CategoryName({ row }: { row: CategoryRecord }) {
    return (
        <Link href={edit(row.id).url} className="flex items-center gap-3 hover:underline">
            {row.image_url ? (
                <img src={row.image_url} alt="" className="size-9 shrink-0 rounded-md border object-cover" loading="lazy" />
            ) : (
                <span className="flex size-9 shrink-0 items-center justify-center rounded-md border bg-muted text-muted-foreground">
                    <ImageOff className="size-4" />
                </span>
            )}
            <span className="grid min-w-0">
                <span className="truncate font-medium">{row.name}</span>
                <span className="truncate text-xs text-muted-foreground">/{row.slug}</span>
            </span>
        </Link>
    );
}

export function createCategoryGridSchema(): ColumnDef<CategoryRecord>[] {
    return [
        { key: 'name', header: 'Category', sortable: true, cell: (row) => <CategoryName row={row} />, hideable: false },
        {
            key: 'description',
            header: 'Description',
            priorityLevel: 3,
            cell: (row) => <span className="line-clamp-1 max-w-xs text-muted-foreground">{row.description ?? '—'}</span>,
        },
        { key: 'products_count', header: 'Products', sortable: true, align: 'right', cell: (row) => <CountChip value={row.products_count} /> },
        { key: 'sort_order', header: 'Order', sortable: true, align: 'right', priorityLevel: 2, cell: (row) => row.sort_order },
        {
            key: 'is_active',
            header: 'Visible',
            sortable: true,
            align: 'center',
            cell: (row) => (
                <StatusSwitch
                    checked={row.is_active}
                    url={toggle(row.id).url}
                    permission={CategoryPermission.CategoriesEdit}
                    label={`Show ${row.name} on the storefront`}
                />
            ),
        },
        { key: 'created_at', header: 'Created', sortable: true, priorityLevel: 3, cell: (row) => <DateFormatted value={row.created_at} /> },
    ];
}
