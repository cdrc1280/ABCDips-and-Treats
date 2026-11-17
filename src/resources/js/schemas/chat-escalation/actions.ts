import { CheckCircle2, Pencil, Reply, RotateCcw, Trash2 } from 'lucide-react';

import { ChatEscalationPermission } from '@/lib/permissions';
import { destroy, edit, reopen, reply, resolve } from '@/routes/admin/chat-escalations';
import type { ChatEscalationRecord, PageAction, RowAction } from '@/types';

export const chatEscalationActionSchema: PageAction[] = [];

export function createChatEscalationActionSchema(): RowAction<ChatEscalationRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: ChatEscalationPermission.ChatEscalationsEdit },
        {
            label: 'Reply',
            icon: Reply,
            href: (row) => reply(row.id).url,
            method: 'patch',
            permission: ChatEscalationPermission.ChatEscalationsEdit,
            prompt: { field: 'message', label: 'Your reply', type: 'text' },
        },
        {
            label: 'Mark resolved',
            icon: CheckCircle2,
            href: (row) => resolve(row.id).url,
            method: 'patch',
            permission: ChatEscalationPermission.ChatEscalationsEdit,
            hidden: (row) => row.status === 'resolved',
        },
        {
            label: 'Reopen',
            icon: RotateCcw,
            href: (row) => reopen(row.id).url,
            method: 'patch',
            permission: ChatEscalationPermission.ChatEscalationsEdit,
            hidden: (row) => row.status !== 'resolved',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: ChatEscalationPermission.ChatEscalationsDelete,
            confirmTitle: 'Delete this support escalation?',
            confirmDescription: (row) => `"${row.guest_name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
