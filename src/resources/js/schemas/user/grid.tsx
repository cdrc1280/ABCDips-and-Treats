import { BooleanBadge } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { edit } from '@/routes/admin/users';
import type { UserRecord, ColumnDef } from '@/types';

export function createUserGridSchema(): ColumnDef<UserRecord>[] {
    return [
        {
            key: 'name',
            header: 'Name',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.name}</span>
                    {row.email && <span className="truncate text-xs text-muted-foreground">{row.email}</span>}
                </Link>
            ),
        },
        { key: 'roles_label', header: 'Roles', cell: (row) => row.roles_label ?? '—' },
        { key: 'phone', header: 'Phone', sortable: true, cell: (row) => row.phone ?? '—', priorityLevel: 3 },
        {
            key: 'email_verified',
            header: 'Verified',
            cell: (row) => <BooleanBadge value={row.email_verified} yes="Yes" no="No" />,
            priorityLevel: 2,
            align: 'center',
        },
        { key: 'created_at', header: 'Added', sortable: true, cell: (row) => <DateFormatted value={row.created_at} />, priorityLevel: 3 },
    ];
}
