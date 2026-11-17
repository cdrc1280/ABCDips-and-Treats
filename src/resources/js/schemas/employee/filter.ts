import type { FilterField, SearchFieldsOf } from '@/types';

export const employeeFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, number, email or phone' },
    {
        name: 'role_title',
        label: 'Role',
        type: 'select',
        options: [
            { value: 'Head Baker', label: 'Head Baker' },
            { value: 'Assistant Baker', label: 'Assistant Baker' },
            { value: 'Cashier', label: 'Cashier' },
            { value: 'Pastry Staff', label: 'Pastry Staff' },
            { value: 'Delivery Rider', label: 'Delivery Rider' },
            { value: 'Manager', label: 'Manager' },
        ],
    },
    {
        name: 'employment_type',
        label: 'Employment',
        type: 'select',
        options: [
            { value: 'full_time', label: 'Full-time regular' },
            { value: 'part_time', label: 'Part-time flex' },
        ],
    },
    { name: 'is_active', label: 'Active', type: 'boolean' },
] as const satisfies readonly FilterField[];

export type EmployeeSearchField = SearchFieldsOf<typeof employeeFilter>;
