import { Pencil, Plus, Trash2 } from 'lucide-react';

import { SupplierPermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/suppliers';
import type { SupplierRecord, PageAction, RowAction } from '@/types';

export const supplierActionSchema: PageAction[] = [
    { label: 'New supplier', icon: Plus, href: create().url, permission: SupplierPermission.SuppliersCreate },
];

export function createSupplierActionSchema(): RowAction<SupplierRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: SupplierPermission.SuppliersEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: SupplierPermission.SuppliersDelete,
            hidden: (row) => row.in_use,
            confirmTitle: 'Delete this supplier?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
