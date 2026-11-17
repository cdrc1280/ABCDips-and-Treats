import { Pencil, Plus, Trash2 } from 'lucide-react';

import { ProductCostingPermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/product-costings';
import type { ProductCostingRecord, PageAction, RowAction } from '@/types';

export const productCostingActionSchema: PageAction[] = [
    { label: 'New product costing', icon: Plus, href: create().url, permission: ProductCostingPermission.ProductCostingsCreate },
];

export function createProductCostingActionSchema(): RowAction<ProductCostingRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: ProductCostingPermission.ProductCostingsEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: ProductCostingPermission.ProductCostingsDelete,
            confirmTitle: 'Delete this product costing?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
