import type { FilterField, SearchFieldsOf } from '@/types';

export const packagingMaterialFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Name, SKU or type' },
    {
        name: 'type',
        label: 'Type',
        type: 'select',
        options: [
            { value: 'box', label: 'Box' },
            { value: 'bag', label: 'Bag' },
            { value: 'board', label: 'Board' },
            { value: 'container', label: 'Container' },
            { value: 'ribbon', label: 'Ribbon' },
            { value: 'label', label: 'Label' },
            { value: 'sticker', label: 'Sticker' },
            { value: 'tape', label: 'Tape' },
            { value: 'wrap', label: 'Wrap' },
            { value: 'other', label: 'Other' },
        ],
    },
    { name: 'is_active', label: 'Active', type: 'boolean' },
] as const satisfies readonly FilterField[];

export type PackagingMaterialSearchField = SearchFieldsOf<typeof packagingMaterialFilter>;
