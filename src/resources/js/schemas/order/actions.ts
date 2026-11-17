import { ArrowRightLeft, Pencil } from 'lucide-react';

import { OrderPermission } from '@/lib/permissions';
import { changeStatus, edit } from '@/routes/admin/orders';
import type { OrderRecord, PageAction, RowAction } from '@/types';

const STATUS_LABELS: Record<string, string> = {
    pending: 'Pending',
    confirmed: 'Confirmed',
    preparing: 'Preparing',
    packaging: 'Packaging',
    out_for_delivery: 'Out for delivery',
    ready_for_pickup: 'Ready for pickup',
    completed: 'Completed',
    cancelled: 'Cancelled',
    refunded: 'Refunded',
    archived: 'Archived',
};

export const orderActionSchema: PageAction[] = [];

export function createOrderActionSchema(): RowAction<OrderRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: OrderPermission.OrdersEdit },
        {
            label: 'Change status',
            icon: ArrowRightLeft,
            href: (row) => changeStatus(row.id).url,
            method: 'patch',
            permission: OrderPermission.OrdersEdit,
            hidden: (row) => row.next_statuses.length === 0,
            prompt: {
                field: 'status',
                label: 'New status',
                type: 'select',
                description: (row) => `Currently ${row.status_label ?? row.status}. Cancelling or refunding returns ingredients to stock.`,
                // Only the moves the server allows from this order's current status.
                options: (row) => row.next_statuses.map((value) => ({ value, label: STATUS_LABELS[value] ?? value })),
            },
        },
    ];
}
