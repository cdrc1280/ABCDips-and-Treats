import { Pencil, Plus, Trash2 } from 'lucide-react';

import { UserPermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/users';
import type { UserRecord, PageAction, RowAction } from '@/types';

export const userActionSchema: PageAction[] = [
    { label: 'New staff account', icon: Plus, href: create().url, permission: UserPermission.UsersCreate },
];

export function createUserActionSchema(): RowAction<UserRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: UserPermission.UsersEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: UserPermission.UsersDelete,
            hidden: (row) => row.is_self,
            confirmTitle: 'Delete this staff account?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
