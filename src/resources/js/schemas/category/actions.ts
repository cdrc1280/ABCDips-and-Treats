import { Pencil, Plus, Trash2 } from 'lucide-react';

import { CategoryPermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/categories';
import type { CategoryRecord, PageAction, RowAction } from '@/types';

export const categoryActionSchema: PageAction[] = [
    { label: 'New category', icon: Plus, href: create().url, permission: CategoryPermission.CategoriesCreate },
];

export function createCategoryActionSchema(): RowAction<CategoryRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: CategoryPermission.CategoriesEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: CategoryPermission.CategoriesDelete,
            hidden: (row) => row.has_products,
            confirmTitle: 'Delete this category?',
            confirmDescription: (row) => `"${row.name}" will be removed from the catalog. This can't be undone from the back office.`,
        },
    ];
}
