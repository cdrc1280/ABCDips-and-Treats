import type { FilterField, SearchFieldsOf } from '@/types';

export const payrollFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Payroll number or notes' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'draft', label: 'Draft' },
            { value: 'approved', label: 'Approved' },
        ],
    },
] as const satisfies readonly FilterField[];

export type PayrollSearchField = SearchFieldsOf<typeof payrollFilter>;
