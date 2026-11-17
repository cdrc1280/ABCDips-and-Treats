import type { FilterField, SearchFieldsOf } from '@/types';

export const reviewFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Reviewer, title, comment or product' },
    { name: 'product_id', label: 'Product', type: 'select', optionsProp: 'product_options' },
    {
        name: 'rating',
        label: 'Rating',
        type: 'select',
        options: [
            { value: '5', label: '5 stars' },
            { value: '4', label: '4 stars' },
            { value: '3', label: '3 stars' },
            { value: '2', label: '2 stars' },
            { value: '1', label: '1 star' },
        ],
    },
    { name: 'is_approved', label: 'Approved', type: 'boolean' },
    { name: 'is_featured', label: 'Featured', type: 'boolean' },
] as const satisfies readonly FilterField[];

export type ReviewSearchField = SearchFieldsOf<typeof reviewFilter>;
