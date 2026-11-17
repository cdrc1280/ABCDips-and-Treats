import { Pencil, Plus, Trash2 } from 'lucide-react';

import { RolePermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/roles';
import type { RoleRecord, PageAction, RowAction } from '@/types';

export const roleActionSchema: PageAction[] = [{ label: 'New role', icon: Plus, href: create().url, permission: RolePermission.RolesCreate }];

export function createRoleActionSchema(): RowAction<RoleRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: RolePermission.RolesEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: RolePermission.RolesDelete,
            hidden: (row) => row.is_system || row.users_count > 0,
            confirmTitle: 'Delete this role?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
