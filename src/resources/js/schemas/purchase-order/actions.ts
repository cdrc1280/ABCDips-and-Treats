import { PackageCheck, Pencil, Plus, Trash2 } from 'lucide-react';

import { PurchaseOrderPermission } from '@/lib/permissions';
import { create, destroy, edit, receive } from '@/routes/admin/purchase-orders';
import type { PurchaseOrderRecord, PageAction, RowAction } from '@/types';

export const purchaseOrderActionSchema: PageAction[] = [
    { label: 'New purchase order', icon: Plus, href: create().url, permission: PurchaseOrderPermission.PurchaseOrdersCreate },
];

export function createPurchaseOrderActionSchema(): RowAction<PurchaseOrderRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: PurchaseOrderPermission.PurchaseOrdersEdit },
        {
            label: 'Receive',
            icon: PackageCheck,
            href: (row) => receive(row.id).url,
            method: 'patch',
            permission: PurchaseOrderPermission.PurchaseOrdersEdit,
            hidden: (row) => row.is_closed,
            isWithConfirmation: true,
            confirmTitle: 'Mark this PO as received?',
            confirmDescription: 'Every item is added to ingredient stock at the PO unit cost.',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: PurchaseOrderPermission.PurchaseOrdersDelete,
            hidden: (row) => row.status === 'received',
            confirmTitle: 'Delete this purchase order?',
            confirmDescription: (row) => `"${row.po_number}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
