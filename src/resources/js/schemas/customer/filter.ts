import type { FilterField, SearchFieldsOf } from '@/types';

export const customerFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, email, phone or city' },
] as const satisfies readonly FilterField[];

export type CustomerSearchField = SearchFieldsOf<typeof customerFilter>;
