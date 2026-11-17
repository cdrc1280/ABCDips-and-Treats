import type { FilterField, SearchFieldsOf } from '@/types';

export const productCostingFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Sheet or product' },
] as const satisfies readonly FilterField[];

export type ProductCostingSearchField = SearchFieldsOf<typeof productCostingFilter>;
