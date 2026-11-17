import type { FilterField, SearchFieldsOf } from '@/types';

export const chatEscalationFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Customer name or email' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'open', label: 'Open' },
            { value: 'in_progress', label: 'In progress' },
            { value: 'resolved', label: 'Resolved' },
        ],
    },
] as const satisfies readonly FilterField[];

export type ChatEscalationSearchField = SearchFieldsOf<typeof chatEscalationFilter>;
