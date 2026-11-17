import type { FilterField, SearchFieldsOf } from '@/types';

export const categoryFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, slug or description' },
    { name: 'is_active', label: 'Status', type: 'boolean' },
] as const satisfies readonly FilterField[];

export type CategorySearchField = SearchFieldsOf<typeof categoryFilter>;
