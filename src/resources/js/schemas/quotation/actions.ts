import { ArrowRightCircle, Pencil, Plus, Trash2 } from 'lucide-react';

import { QuotationPermission } from '@/lib/permissions';
import { convert, create, destroy, edit } from '@/routes/admin/quotations';
import type { QuotationRecord, PageAction, RowAction } from '@/types';

export const quotationActionSchema: PageAction[] = [
    { label: 'New quotation', icon: Plus, href: create().url, permission: QuotationPermission.QuotationsCreate },
];

export function createQuotationActionSchema(): RowAction<QuotationRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: QuotationPermission.QuotationsEdit },
        {
            label: 'Convert to PO',
            icon: ArrowRightCircle,
            href: (row) => convert(row.id).url,
            method: 'patch',
            permission: QuotationPermission.QuotationsEdit,
            hidden: (row) => row.is_converted,
            isWithConfirmation: true,
            confirmTitle: 'Create a purchase order from this quotation?',
            confirmDescription: 'A draft PO is created with the same supplier, items and totals.',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: QuotationPermission.QuotationsDelete,
            hidden: (row) => row.is_converted,
            confirmTitle: 'Delete this quotation?',
            confirmDescription: (row) => `"${row.quotation_number}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
