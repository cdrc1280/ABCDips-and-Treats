import { BadgeCheck, Banknote, Pencil, Plus, Trash2 } from 'lucide-react';

import { PayrollPermission } from '@/lib/permissions';
import { approve, create, destroy, edit, markPaid } from '@/routes/admin/payrolls';
import type { PayrollRecord, PageAction, RowAction } from '@/types';

export const payrollActionSchema: PageAction[] = [
    { label: 'New payroll', icon: Plus, href: create().url, permission: PayrollPermission.PayrollsCreate },
];

export function createPayrollActionSchema(): RowAction<PayrollRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: PayrollPermission.PayrollsEdit },
        {
            label: 'Approve',
            icon: BadgeCheck,
            href: (row) => approve(row.id).url,
            method: 'patch',
            permission: PayrollPermission.PayrollsEdit,
            hidden: (row) => row.status !== 'draft',
        },
        {
            label: 'Mark paid',
            icon: Banknote,
            href: (row) => markPaid(row.id).url,
            method: 'patch',
            permission: PayrollPermission.PayrollsEdit,
            hidden: (row) => row.is_paid,
            isWithConfirmation: true,
            confirmTitle: 'Mark this payroll as paid?',
            confirmDescription: 'The pay run is locked after this.',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: PayrollPermission.PayrollsDelete,
            hidden: (row) => row.is_paid,
            confirmTitle: 'Delete this payroll?',
            confirmDescription: (row) => `"${row.payroll_number}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
