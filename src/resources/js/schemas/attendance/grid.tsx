import { BooleanBadge, StatusBadge, toneFor } from '@/renderers/cells/badges';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { edit } from '@/routes/admin/attendances';
import { formatNumber } from '@/lib/format';
import type { AttendanceRecord, ColumnDef } from '@/types';

export function createAttendanceGridSchema(): ColumnDef<AttendanceRecord>[] {
    return [
        {
            key: 'employee_name',
            header: 'Employee',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.employee_name}</span>
                    {row.date && <span className="truncate text-xs text-muted-foreground">{row.date}</span>}
                </Link>
            ),
        },
        { key: 'date', header: 'Date', sortable: true, cell: (row) => <DateFormatted value={row.date} /> },
        {
            key: 'status',
            header: 'Status',
            sortable: true,
            cell: (row) => <StatusBadge label={row.status_label ?? row.status} tone={toneFor(String(row.status))} />,
        },
        { key: 'hours_worked', header: 'Hours', sortable: true, cell: (row) => `${formatNumber(row.hours_worked)}`, align: 'right' },
        {
            key: 'overtime_hours',
            header: 'OT hours',
            sortable: true,
            cell: (row) => `${formatNumber(row.overtime_hours)}`,
            priorityLevel: 2,
            align: 'right',
        },
        {
            key: 'is_holiday',
            header: 'Holiday',
            sortable: true,
            cell: (row) => <BooleanBadge value={row.is_holiday} yes="Yes" no="No" />,
            priorityLevel: 3,
            align: 'center',
        },
    ];
}
