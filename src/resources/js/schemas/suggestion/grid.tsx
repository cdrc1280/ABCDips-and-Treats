import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/suggestions';
import type { SuggestionRecord, ColumnDef } from '@/types';

export function createSuggestionGridSchema(): ColumnDef<SuggestionRecord>[] {
    return [
        {
            key: 'subject',
            header: 'Suggestion',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.subject}</span>
                    {row.name && <span className="truncate text-xs text-muted-foreground">{row.name}</span>}
                </Link>
            ),
        },
        { key: 'category_label', header: 'Category', cell: (row) => row.category_label ?? '—', priorityLevel: 2 },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        { key: 'created_at', header: 'Received', sortable: true, cell: (row) => <DateFormatted value={row.created_at} /> },
    ];
}
