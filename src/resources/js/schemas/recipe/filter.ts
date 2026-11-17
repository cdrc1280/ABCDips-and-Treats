import type { FilterField, SearchFieldsOf } from '@/types';

export const recipeFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Recipe or product' },
    { name: 'product_id', label: 'Product', type: 'select', optionsProp: 'product_options' },
] as const satisfies readonly FilterField[];

export type RecipeSearchField = SearchFieldsOf<typeof recipeFilter>;
