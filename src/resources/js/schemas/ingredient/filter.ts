import type { FilterField, SearchFieldsOf } from '@/types';

export const ingredientFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, SKU or supplier' },
    { name: 'supplier_id', label: 'Supplier', type: 'select', optionsProp: 'supplier_options' },
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

export type IngredientSearchField = SearchFieldsOf<typeof ingredientFilter>;
