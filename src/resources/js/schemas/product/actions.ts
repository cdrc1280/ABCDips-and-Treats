import { Pencil, Plus, Trash2 } from 'lucide-react';

import { ProductPermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/products';
import type { ProductRecord, PageAction, RowAction } from '@/types';

export const productActionSchema: PageAction[] = [
    { label: 'New product', icon: Plus, href: create().url, permission: ProductPermission.ProductsCreate },
];

export function createProductActionSchema(): RowAction<ProductRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: ProductPermission.ProductsEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: ProductPermission.ProductsDelete,
            hidden: (row) => row.has_orders,
            confirmTitle: 'Delete this product?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
