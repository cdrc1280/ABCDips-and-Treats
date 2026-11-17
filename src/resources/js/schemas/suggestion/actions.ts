import { CheckCircle2, Pencil, Search, Trash2 } from 'lucide-react';

import { SuggestionPermission } from '@/lib/permissions';
import { destroy, edit, markImplemented, markReviewing } from '@/routes/admin/suggestions';
import type { SuggestionRecord, PageAction, RowAction } from '@/types';

export const suggestionActionSchema: PageAction[] = [];

export function createSuggestionActionSchema(): RowAction<SuggestionRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: SuggestionPermission.SuggestionsEdit },
        {
            label: 'Start reviewing',
            icon: Search,
            href: (row) => markReviewing(row.id).url,
            method: 'patch',
            permission: SuggestionPermission.SuggestionsEdit,
            hidden: (row) => row.status !== 'new',
        },
        {
            label: 'Mark implemented',
            icon: CheckCircle2,
            href: (row) => markImplemented(row.id).url,
            method: 'patch',
            permission: SuggestionPermission.SuggestionsEdit,
            hidden: (row) => row.status === 'implemented',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: SuggestionPermission.SuggestionsDelete,
            confirmTitle: 'Delete this suggestion?',
            confirmDescription: (row) => `"${row.subject}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
