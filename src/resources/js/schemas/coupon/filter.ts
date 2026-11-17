import type { FilterField, SearchFieldsOf } from '@/types';

export const couponFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Coupon code' },
    {
        name: 'type',
        label: 'Type',
        type: 'select',
        options: [
            { value: 'fixed', label: 'Fixed amount (₱)' },
            { value: 'percent', label: 'Percentage (%)' },
        ],
    },
    { name: 'is_active', label: 'Active', type: 'boolean' },
] as const satisfies readonly FilterField[];

export type CouponSearchField = SearchFieldsOf<typeof couponFilter>;
