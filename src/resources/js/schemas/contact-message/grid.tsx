import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { edit } from '@/routes/admin/contact-messages';
import type { ContactMessageRecord, ColumnDef } from '@/types';

export function createContactMessageGridSchema(): ColumnDef<ContactMessageRecord>[] {
    return [
        {
            key: 'subject',
            header: 'Subject',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.subject}</span>
                    {row.name && <span className="truncate text-xs text-muted-foreground">{row.name}</span>}
                </Link>
            ),
        },
        { key: 'email', header: 'Email', sortable: true, cell: (row) => row.email ?? '—', priorityLevel: 2 },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        { key: 'created_at', header: 'Received', sortable: true, cell: (row) => <DateFormatted value={row.created_at} withTime /> },
    ];
}
