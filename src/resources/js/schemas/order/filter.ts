import type { FilterField, SearchFieldsOf } from '@/types';

export const orderFilter = [
    { name: 'keyword', label: 'Search', type: 'text', placeholder: 'Order no., customer, email, phone or payment ref.' },
    {
        name: 'status',
        label: 'Status',
        type: 'select',
        options: [
            { value: 'pending', label: 'Pending' },
            { value: 'confirmed', label: 'Confirmed' },
            { value: 'preparing', label: 'Preparing' },
            { value: 'packaging', label: 'Packaging' },
            { value: 'out_for_delivery', label: 'Out for delivery' },
            { value: 'ready_for_pickup', label: 'Ready for pickup' },
            { value: 'completed', label: 'Completed' },
            { value: 'cancelled', label: 'Cancelled' },
            { value: 'refunded', label: 'Refunded' },
            { value: 'archived', label: 'Archived' },
        ],
    },
    {
        name: 'payment_status',
        label: 'Payment',
        type: 'select',
        options: [
            { value: 'pending', label: 'Pending' },
            { value: 'awaiting_payment', label: 'Awaiting payment' },
            { value: 'paid', label: 'Paid' },
            { value: 'failed', label: 'Failed' },
            { value: 'refunded', label: 'Refunded' },
        ],
    },
    {
        name: 'fulfillment_type',
        label: 'Fulfillment',
        type: 'select',
        options: [
            { value: 'delivery', label: 'Delivery' },
            { value: 'pickup', label: 'Pickup' },
        ],
    },
    { name: 'from', label: 'From', type: 'date' },
    { name: 'to', label: 'To', type: 'date' },
] as const satisfies readonly FilterField[];

export type OrderSearchField = SearchFieldsOf<typeof orderFilter>;
