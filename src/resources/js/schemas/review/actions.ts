import { Pencil, Plus, Sparkles, Trash2 } from 'lucide-react';

import { ReviewPermission } from '@/lib/permissions';
import { create, destroy, edit, feature } from '@/routes/admin/reviews';
import type { ReviewRecord, PageAction, RowAction } from '@/types';

export const reviewActionSchema: PageAction[] = [{ label: 'New review', icon: Plus, href: create().url, permission: ReviewPermission.ReviewsCreate }];

export function createReviewActionSchema(): RowAction<ReviewRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: ReviewPermission.ReviewsEdit },
        { label: 'Toggle featured', icon: Sparkles, href: (row) => feature(row.id).url, method: 'patch', permission: ReviewPermission.ReviewsEdit },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: ReviewPermission.ReviewsDelete,
            confirmTitle: 'Delete this review?',
            confirmDescription: (row) => `"${row.reviewer_name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
