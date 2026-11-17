import type { FilterField, SearchFieldsOf } from '@/types';

export const userFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, email or phone' },
    {
        name: 'role',
        label: 'Role',
        type: 'select',
        options: [
            { value: 'super_admin', label: 'Super admin' },
            { value: 'admin', label: 'Admin' },
            { value: 'manager', label: 'Manager' },
            { value: 'staff', label: 'Staff' },
        ],
    },
] as const satisfies readonly FilterField[];

export type UserSearchField = SearchFieldsOf<typeof userFilter>;
