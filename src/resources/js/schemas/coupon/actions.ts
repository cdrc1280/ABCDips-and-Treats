import { Pencil, Plus, Trash2 } from 'lucide-react';

import { CouponPermission } from '@/lib/permissions';
import { create, destroy, edit } from '@/routes/admin/coupons';
import type { CouponRecord, PageAction, RowAction } from '@/types';

export const couponActionSchema: PageAction[] = [{ label: 'New coupon', icon: Plus, href: create().url, permission: CouponPermission.CouponsCreate }];

export function createCouponActionSchema(): RowAction<CouponRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: CouponPermission.CouponsEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: CouponPermission.CouponsDelete,
            confirmTitle: 'Delete this coupon?',
            confirmDescription: (row) => `"${row.code}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
