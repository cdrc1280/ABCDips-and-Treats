import type { FilterField, SearchFieldsOf } from '@/types';

export const contactMessageFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, email, subject or message' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'unread', label: 'Unread' },
            { value: 'read', label: 'Read' },
            { value: 'replied', label: 'Replied' },
            { value: 'archived', label: 'Archived' },
        ],
    },
] as const satisfies readonly FilterField[];

export type ContactMessageSearchField = SearchFieldsOf<typeof contactMessageFilter>;
