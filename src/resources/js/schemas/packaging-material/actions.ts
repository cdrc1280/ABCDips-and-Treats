import { PackagePlus, Pencil, Plus, Trash2 } from 'lucide-react';

import { PackagingMaterialPermission } from '@/lib/permissions';
import { create, destroy, edit, restock } from '@/routes/admin/packaging-materials';
import type { PackagingMaterialRecord, PageAction, RowAction } from '@/types';

export const packagingMaterialActionSchema: PageAction[] = [
    { label: 'New packaging material', icon: Plus, href: create().url, permission: PackagingMaterialPermission.PackagingMaterialsCreate },
];

export function createPackagingMaterialActionSchema(): RowAction<PackagingMaterialRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: PackagingMaterialPermission.PackagingMaterialsEdit },
        {
            label: 'Add stock',
            icon: PackagePlus,
            href: (row) => restock(row.id).url,
            method: 'patch',
            permission: PackagingMaterialPermission.PackagingMaterialsEdit,
            prompt: { field: 'add_qty', label: 'Quantity received', type: 'number', min: 1, step: '1' },
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: PackagingMaterialPermission.PackagingMaterialsDelete,
            confirmTitle: 'Delete this packaging material?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
