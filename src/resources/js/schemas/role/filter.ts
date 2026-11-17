import type { FilterField, SearchFieldsOf } from '@/types';

export const roleFilter = [{ name: 'keyword', label: 'Search', type: 'text', placeholder: 'Role name' }] as const satisfies readonly FilterField[];

export type RoleSearchField = SearchFieldsOf<typeof roleFilter>;
