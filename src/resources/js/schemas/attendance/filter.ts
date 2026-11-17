import type { FilterField, SearchFieldsOf } from '@/types';

export const attendanceFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Employee name or notes' },
    { name: 'employee_id', label: 'Employee', type: 'select', optionsProp: 'employee_options' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'present', label: 'Present' },
            { value: 'absent', label: 'Absent' },
            { value: 'late', label: 'Late' },
            { value: 'half_day', label: 'Half day' },
            { value: 'on_leave', label: 'On leave' },
        ],
    },
    { name: 'from', label: 'From', type: 'date' },
    { name: 'to', label: 'To', type: 'date' },
] as const satisfies readonly FilterField[];

export type AttendanceSearchField = SearchFieldsOf<typeof attendanceFilter>;
