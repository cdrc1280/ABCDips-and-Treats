import type { FilterField, SearchFieldsOf } from '@/types';

export const productFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, SKU, barcode or category' },
    { name: 'category_id', label: 'Category', type: 'select', optionsProp: 'category_options' },
    { name: 'is_active', label: 'Active', type: 'boolean' },
    { name: 'is_featured', label: 'Featured', type: 'boolean' },
    {
        name: 'stock',
        label: 'Stock',
        type: 'select',
        options: [
            { value: 'low', label: 'Low or out of stock' },
            { value: 'ok', label: 'In stock' },
        ],
    },
] as const satisfies readonly FilterField[];

export type ProductSearchField = SearchFieldsOf<typeof productFilter>;
