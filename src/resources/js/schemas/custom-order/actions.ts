import { CheckCircle2, Flame, Pencil, Plus, Trash2 } from 'lucide-react';

import { CustomOrderPermission } from '@/lib/permissions';
import { complete, create, destroy, edit, startProduction } from '@/routes/admin/custom-orders';
import type { CustomOrderRecord, PageAction, RowAction } from '@/types';

export const customOrderActionSchema: PageAction[] = [
    { label: 'New custom order', icon: Plus, href: create().url, permission: CustomOrderPermission.CustomOrdersCreate },
];

export function createCustomOrderActionSchema(): RowAction<CustomOrderRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: CustomOrderPermission.CustomOrdersEdit },
        {
            label: 'Start production',
            icon: Flame,
            href: (row) => startProduction(row.id).url,
            method: 'patch',
            permission: CustomOrderPermission.CustomOrdersEdit,
            hidden: (row) => !['inquiry', 'quoted', 'deposit_paid'].includes(row.status),
            isWithConfirmation: true,
            confirmTitle: 'Start production?',
            confirmDescription: 'Ingredients for this cake are deducted from inventory.',
        },
        {
            label: 'Mark completed',
            icon: CheckCircle2,
            href: (row) => complete(row.id).url,
            method: 'patch',
            permission: CustomOrderPermission.CustomOrdersEdit,
            hidden: (row) => ['completed', 'cancelled'].includes(row.status),
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: CustomOrderPermission.CustomOrdersDelete,
            confirmTitle: 'Delete this custom order?',
            confirmDescription: (row) => `"${row.reference_number}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
