import type { FilterField, SearchFieldsOf } from '@/types';

export const deliveryPoolFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Pool code, zone or area' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'open', label: 'Open' },
            { value: 'building', label: 'Building' },
            { value: 'settled', label: 'Settled' },
            { value: 'dispatched', label: 'Dispatched' },
        ],
    },
] as const satisfies readonly FilterField[];

export type DeliveryPoolSearchField = SearchFieldsOf<typeof deliveryPoolFilter>;
