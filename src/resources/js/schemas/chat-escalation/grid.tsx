import { CountChip, StatusBadge, toneFor } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { edit } from '@/routes/admin/chat-escalations';
import type { ChatEscalationRecord, ColumnDef } from '@/types';

export function createChatEscalationGridSchema(): ColumnDef<ChatEscalationRecord>[] {
    return [
        {
            key: 'customer_name',
            header: 'Customer',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.customer_name}</span>
                    {row.last_message && <span className="truncate text-xs text-muted-foreground">{row.last_message}</span>}
                </Link>
            ),
        },
        { key: 'messages_count', header: 'Messages', cell: (row) => <CountChip value={row.messages_count} />, align: 'right' },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        { key: 'updated_at', header: 'Last activity', sortable: true, cell: (row) => <DateFormatted value={row.updated_at} withTime /> },
    ];
}
