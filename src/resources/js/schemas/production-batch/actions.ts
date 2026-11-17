import { CheckCircle2, Pencil, Plus, Trash2 } from 'lucide-react';

import { ProductionBatchPermission } from '@/lib/permissions';
import { complete, create, destroy, edit } from '@/routes/admin/production-batches';
import type { ProductionBatchRecord, PageAction, RowAction } from '@/types';

export const productionBatchActionSchema: PageAction[] = [
    { label: 'New production batch', icon: Plus, href: create().url, permission: ProductionBatchPermission.ProductionBatchesCreate },
];

export function createProductionBatchActionSchema(): RowAction<ProductionBatchRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: ProductionBatchPermission.ProductionBatchesEdit },
        {
            label: 'Complete batch',
            icon: CheckCircle2,
            href: (row) => complete(row.id).url,
            method: 'patch',
            permission: ProductionBatchPermission.ProductionBatchesEdit,
            hidden: (row) => row.is_completed,
            isWithConfirmation: true,
            confirmTitle: 'Complete this batch?',
            confirmDescription: 'Ingredients are deducted from inventory and the finished quantity is added to product stock.',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: ProductionBatchPermission.ProductionBatchesDelete,
            hidden: (row) => row.is_completed,
            confirmTitle: 'Delete this production batch?',
            confirmDescription: (row) => `"${row.batch_number}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
