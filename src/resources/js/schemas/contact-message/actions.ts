import { MailCheck, MailOpen, Pencil, Trash2 } from 'lucide-react';

import { ContactMessagePermission } from '@/lib/permissions';
import { destroy, edit, markRead, markReplied } from '@/routes/admin/contact-messages';
import type { ContactMessageRecord, PageAction, RowAction } from '@/types';

export const contactMessageActionSchema: PageAction[] = [];

export function createContactMessageActionSchema(): RowAction<ContactMessageRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: ContactMessagePermission.ContactMessagesEdit },
        {
            label: 'Mark as read',
            icon: MailOpen,
            href: (row) => markRead(row.id).url,
            method: 'patch',
            permission: ContactMessagePermission.ContactMessagesEdit,
            hidden: (row) => row.status !== 'unread',
        },
        {
            label: 'Mark as replied',
            icon: MailCheck,
            href: (row) => markReplied(row.id).url,
            method: 'patch',
            permission: ContactMessagePermission.ContactMessagesEdit,
            hidden: (row) => row.status === 'replied',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: ContactMessagePermission.ContactMessagesDelete,
            confirmTitle: 'Delete this contact message?',
            confirmDescription: (row) => `"${row.subject}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
