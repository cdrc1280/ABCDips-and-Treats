import type { FilterField, SearchFieldsOf } from '@/types';

export const customOrderFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Reference, customer or theme' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'inquiry', label: 'Inquiry' },
            { value: 'quoted', label: 'Quoted' },
            { value: 'deposit_paid', label: 'Deposit paid' },
            { value: 'in_production', label: 'In production' },
            { value: 'ready', label: 'Ready' },
            { value: 'completed', label: 'Completed' },
            { value: 'cancelled', label: 'Cancelled' },
        ],
    },
    { name: 'from', label: 'Event from', type: 'date' },
    { name: 'to', label: 'Event to', type: 'date' },
] as const satisfies readonly FilterField[];

export type CustomOrderSearchField = SearchFieldsOf<typeof customOrderFilter>;
