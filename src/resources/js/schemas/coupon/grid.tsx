import { CountChip, StatusBadge, toneFor } from '@/renderers/cells/badges';
import { CouponPermission } from '@/lib/permissions';
import { DateFormatted } from '@/renderers/cells/date-formatted';
import { Link } from '@inertiajs/react';
import { StatusSwitch } from '@/renderers/cells/status-switch';
import { edit, toggle } from '@/routes/admin/coupons';
import { formatNumber } from '@/lib/format';
import type { CouponRecord, ColumnDef } from '@/types';

export function createCouponGridSchema(): ColumnDef<CouponRecord>[] {
    return [
        {
            key: 'code',
            header: 'Code',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.code}</span>
                </Link>
            ),
        },
        { key: 'type_label', header: 'Type', cell: (row) => row.type_label ?? '—' },
        { key: 'value', header: 'Value', sortable: true, cell: (row) => `${formatNumber(row.value)}`, align: 'right' },
        { key: 'used_count', header: 'Used', sortable: true, cell: (row) => <CountChip value={row.used_count} />, align: 'right' },
        { key: 'state', header: 'Status', cell: (row) => <StatusBadge label={row.state ?? row.state} tone={toneFor(String(row.state))} /> },
        { key: 'expires_at', header: 'Expires', sortable: true, cell: (row) => <DateFormatted value={row.expires_at} withTime />, priorityLevel: 2 },
        {
            key: 'is_active',
            header: 'Active',
            sortable: true,
            cell: (row) => (
                <StatusSwitch
                    checked={row.is_active}
                    field="is_active"
                    url={toggle(row.id).url}
                    permission={CouponPermission.CouponsEdit}
                    label={`Toggle ${row.code}`}
                />
            ),
            align: 'center',
        },
    ];
}
