import { BooleanBadge, CountChip } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { ReviewPermission } from '@/lib/permissions';
import { Star } from 'lucide-react';
import { StatusSwitch } from '@/renderers/cells/status-switch';
import { edit, toggle } from '@/routes/admin/reviews';
import type { ReviewRecord, ColumnDef } from '@/types';

export function createReviewGridSchema(): ColumnDef<ReviewRecord>[] {
    return [
        {
            key: 'reviewer_name',
            header: 'Reviewer',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.reviewer_name}</span>
                    {row.title && <span className="truncate text-xs text-muted-foreground">{row.title}</span>}
                </Link>
            ),
        },
        { key: 'product_name', header: 'Product', cell: (row) => row.product_name ?? '—', priorityLevel: 2 },
        {
            key: 'rating',
            header: 'Rating',
            sortable: true,
            cell: (row) => (
                <span className="inline-flex items-center gap-1 tabular-nums">
                    <Star className="size-3.5 fill-highlight text-highlight" />
                    {row.rating}
                </span>
            ),
        },
        {
            key: 'helpful_votes',
            header: 'Helpful',
            sortable: true,
            cell: (row) => <CountChip value={row.helpful_votes} />,
            priorityLevel: 3,
            align: 'right',
        },
        {
            key: 'is_featured',
            header: 'Featured',
            sortable: true,
            cell: (row) => <BooleanBadge value={row.is_featured} yes="Yes" no="No" />,
            priorityLevel: 2,
            align: 'center',
        },
        { key: 'created_at', header: 'Posted', sortable: true, cell: (row) => <DateFormatted value={row.created_at} />, priorityLevel: 3 },
        {
            key: 'is_approved',
            header: 'Approved',
            sortable: true,
            cell: (row) => (
                <StatusSwitch
                    checked={row.is_approved}
                    field="is_approved"
                    url={toggle(row.id).url}
                    permission={ReviewPermission.ReviewsEdit}
                    label={`Toggle ${row.reviewer_name}`}
                />
            ),
            align: 'center',
        },
    ];
}
