import { BooleanBadge, CountChip } from '@/renderers/cells/badges';
import { Link } from '@inertiajs/react';
import { edit } from '@/routes/admin/roles';
import type { RoleRecord, ColumnDef } from '@/types';

export function createRoleGridSchema(): ColumnDef<RoleRecord>[] {
    return [
        {
            key: 'name',
            header: 'Role',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.name}</span>
                </Link>
            ),
        },
        {
            key: 'permissions_count',
            header: 'Permissions',
            sortable: true,
            cell: (row) => <CountChip value={row.permissions_count} />,
            align: 'right',
        },
        { key: 'users_count', header: 'Users', sortable: true, cell: (row) => <CountChip value={row.users_count} />, align: 'right' },
        { key: 'is_system', header: 'Built-in', cell: (row) => <BooleanBadge value={row.is_system} yes="Yes" no="No" />, align: 'center' },
    ];
}
