import { HandCoins, Pencil, Plus, Trash2 } from 'lucide-react';

import { DeliveryPoolPermission } from '@/lib/permissions';
import { create, destroy, edit, settle } from '@/routes/admin/delivery-pools';
import type { DeliveryPoolRecord, PageAction, RowAction } from '@/types';

export const deliveryPoolActionSchema: PageAction[] = [
    { label: 'New delivery pool', icon: Plus, href: create().url, permission: DeliveryPoolPermission.DeliveryPoolsCreate },
];

export function createDeliveryPoolActionSchema(): RowAction<DeliveryPoolRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: DeliveryPoolPermission.DeliveryPoolsEdit },
        {
            label: 'Settle pool',
            icon: HandCoins,
            href: (row) => settle(row.id).url,
            method: 'patch',
            permission: DeliveryPoolPermission.DeliveryPoolsEdit,
            hidden: (row) => row.is_locked,
            isWithConfirmation: true,
            confirmTitle: 'Settle this pool?',
            confirmDescription: 'Each order’s total is recalculated with its shipping share and the orders move to Confirmed.',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: DeliveryPoolPermission.DeliveryPoolsDelete,
            hidden: (row) => row.orders_count > 0,
            confirmTitle: 'Delete this delivery pool?',
            confirmDescription: (row) => `"${row.pool_code}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
