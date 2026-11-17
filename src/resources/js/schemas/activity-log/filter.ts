import type { FilterField, SearchFieldsOf } from '@/types';

export const activityFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Description or changed values' },
    { name: 'log_name', label: 'Record type', type: 'select', optionsProp: 'log_options' },
    {
        name: 'event',
        label: 'Action',
        type: 'select',
        options: [
            { value: 'created', label: 'Created' },
            { value: 'updated', label: 'Updated' },
            { value: 'deleted', label: 'Deleted' },
        ],
    },
    { name: 'causer_id', label: 'Changed by', type: 'select', optionsProp: 'causer_options' },
    { name: 'from', label: 'From', type: 'date' },
    { name: 'to', label: 'To', type: 'date' },
] as const satisfies readonly FilterField[];

export type ActivitySearchField = SearchFieldsOf<typeof activityFilter>;
