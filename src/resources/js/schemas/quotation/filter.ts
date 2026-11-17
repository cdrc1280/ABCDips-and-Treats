import type { FilterField, SearchFieldsOf } from '@/types';

export const quotationFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Number, supplier or notes' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'draft', label: 'Draft' },
            { value: 'sent', label: 'Sent' },
            { value: 'accepted', label: 'Accepted' },
            { value: 'rejected', label: 'Rejected' },
            { value: 'converted_to_po', label: 'Converted to PO' },
        ],
    },
    { name: 'supplier_id', label: 'Supplier', type: 'select', optionsProp: 'supplier_options' },
] as const satisfies readonly FilterField[];

export type QuotationSearchField = SearchFieldsOf<typeof quotationFilter>;
