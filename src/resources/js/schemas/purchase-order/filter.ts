import type { FilterField, SearchFieldsOf } from '@/types';

export const purchaseOrderFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'PO number, supplier, DR or SI' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'draft', label: 'Draft' },
            { value: 'sent', label: 'Sent' },
            { value: 'cancelled', label: 'Cancelled' },
        ],
    },
    { name: 'supplier_id', label: 'Supplier', type: 'select', optionsProp: 'supplier_options' },
] as const satisfies readonly FilterField[];

export type PurchaseOrderSearchField = SearchFieldsOf<typeof purchaseOrderFilter>;
