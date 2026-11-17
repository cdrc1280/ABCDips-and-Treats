import type { FilterField, SearchFieldsOf } from '@/types';

export const supplierFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, contact, email or phone' },
] as const satisfies readonly FilterField[];

export type SupplierSearchField = SearchFieldsOf<typeof supplierFilter>;
