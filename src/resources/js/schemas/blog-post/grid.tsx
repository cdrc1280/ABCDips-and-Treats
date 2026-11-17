import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/blog-posts';
import type { BlogPostRecord, ColumnDef } from '@/types';

export function createBlogPostGridSchema(): ColumnDef<BlogPostRecord>[] {
    return [
        {
            key: 'title',
            header: 'Post',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.title}</span>
                    {row.category && <span className="truncate text-xs text-muted-foreground">{row.category}</span>}
                </Link>
            ),
        },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        { key: 'author_name', header: 'Author', cell: (row) => row.author_name ?? '—', priorityLevel: 3 },
        { key: 'published_at', header: 'Published', sortable: true, cell: (row) => <DateFormatted value={row.published_at} />, priorityLevel: 2 },
        { key: 'updated_at', header: 'Updated', sortable: true, cell: (row) => <DateFormatted value={row.updated_at} />, priorityLevel: 3 },
    ];
}
