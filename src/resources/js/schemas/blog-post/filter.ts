import type { FilterField, SearchFieldsOf } from '@/types';

export const blogPostFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Title, slug, excerpt or category' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'draft', label: 'Draft' },
            { value: 'published', label: 'Published' },
            { value: 'archived', label: 'Archived' },
        ],
    },
] as const satisfies readonly FilterField[];

export type BlogPostSearchField = SearchFieldsOf<typeof blogPostFilter>;
