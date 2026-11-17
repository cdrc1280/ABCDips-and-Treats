import { titleCase } from '@/lib/format';
import { StatusBadge, type Tone } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import type { ActivityRecord, ColumnDef } from '@/types';

const eventTone: Record<string, Tone> = { created: 'success', updated: 'info', deleted: 'danger' };

export function createActivityGridSchema(): ColumnDef<ActivityRecord>[] {
    return [
        {
            key: 'created_at',
            header: 'When',
            sortField: 'id',
            sortable: true,
            hideable: false,
            cell: (row) => <DateFormatted value={row.created_at} withTime />,
        },
        {
            key: 'event',
            header: 'Action',
            sortable: true,
            cell: (row) => <StatusBadge label={row.event ?? row.description} tone={eventTone[row.event ?? ''] ?? 'neutral'} />,
        },
        {
            key: 'subject',
            header: 'Record',
            cell: (row) => (
                <span className="grid">
                    <span className="font-medium">{titleCase(row.log_name)}</span>
                    {row.subject_id && <span className="text-xs text-muted-foreground">#{row.subject_id}</span>}
                </span>
            ),
        },
        { key: 'causer_name', header: 'By', cell: (row) => row.causer_name ?? <span className="text-muted-foreground">System</span> },
        {
            key: 'changes',
            header: 'Fields',
            priorityLevel: 2,
            cell: (row) => (
                <span className="line-clamp-1 max-w-sm text-muted-foreground">{row.changes.map((change) => change.field).join(', ') || '—'}</span>
            ),
        },
    ];
}
