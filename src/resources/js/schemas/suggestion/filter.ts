import type { FilterField, SearchFieldsOf } from '@/types';

export const suggestionFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, email, subject or message' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'new', label: 'New' },
            { value: 'reviewing', label: 'Reviewing' },
            { value: 'implemented', label: 'Implemented' },
            { value: 'declined', label: 'Declined' },
        ],
    },
    {
        name: 'category',
        label: 'Category',
        type: 'select',
        options: [
            { value: 'product_idea', label: 'Product idea' },
            { value: 'service_feedback', label: 'Service feedback' },
            { value: 'feature_request', label: 'Feature request' },
            { value: 'other', label: 'Other' },
        ],
    },
] as const satisfies readonly FilterField[];

export type SuggestionSearchField = SearchFieldsOf<typeof suggestionFilter>;
