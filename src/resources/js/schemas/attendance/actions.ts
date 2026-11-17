import { Pencil, Plus, Trash2 } from 'lucide-react';

import { AttendancePermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/attendances';
import type { AttendanceRecord, PageAction, RowAction } from '@/types';

export const attendanceActionSchema: PageAction[] = [
    { label: 'New attendance', icon: Plus, href: create().url, permission: AttendancePermission.AttendancesCreate },
];

export function createAttendanceActionSchema(): RowAction<AttendanceRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: AttendancePermission.AttendancesEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: AttendancePermission.AttendancesDelete,
            confirmTitle: 'Delete this attendance?',
            confirmDescription: (row) => `"${row.date}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
