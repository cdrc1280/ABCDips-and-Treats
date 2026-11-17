import type { FilterField, SearchFieldsOf } from '@/types';

export const productionBatchFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Batch number, product or recipe' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'planned', label: 'Planned' },
            { value: 'in_prep', label: 'In prep' },
            { value: 'baking', label: 'Baking' },
            { value: 'completed', label: 'Completed' },
            { value: 'cancelled', label: 'Cancelled' },
        ],
    },
    { name: 'product_id', label: 'Product', type: 'select', optionsProp: 'product_options' },
] as const satisfies readonly FilterField[];

export type ProductionBatchSearchField = SearchFieldsOf<typeof productionBatchFilter>;
