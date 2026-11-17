import { Pencil, Plus, Trash2 } from 'lucide-react';

import { EmployeePermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/employees';
import type { EmployeeRecord, PageAction, RowAction } from '@/types';

export const employeeActionSchema: PageAction[] = [
    { label: 'New employee', icon: Plus, href: create().url, permission: EmployeePermission.EmployeesCreate },
];

export function createEmployeeActionSchema(): RowAction<EmployeeRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: EmployeePermission.EmployeesEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: EmployeePermission.EmployeesDelete,
            confirmTitle: 'Delete this employee?',
            confirmDescription: (row) => `"${row.first_name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
