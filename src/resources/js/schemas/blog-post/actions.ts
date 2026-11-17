import { Pencil, Plus, Send, Trash2 } from 'lucide-react';

import { BlogPostPermission } from '@/lib/permissions';
import { create, destroy, edit, publish } from '@/routes/admin/blog-posts';
import type { BlogPostRecord, PageAction, RowAction } from '@/types';

export const blogPostActionSchema: PageAction[] = [
    { label: 'New blog post', icon: Plus, href: create().url, permission: BlogPostPermission.BlogPostsCreate },
];

export function createBlogPostActionSchema(): RowAction<BlogPostRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: BlogPostPermission.BlogPostsEdit },
        {
            label: 'Publish now',
            icon: Send,
            href: (row) => publish(row.id).url,
            method: 'patch',
            permission: BlogPostPermission.BlogPostsEdit,
            hidden: (row) => row.status === 'published',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: BlogPostPermission.BlogPostsDelete,
            confirmTitle: 'Delete this blog post?',
            confirmDescription: (row) => `"${row.title}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
