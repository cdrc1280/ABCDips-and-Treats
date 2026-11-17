import { DateFormatted } from '@/renderers/cells/date-formatted';
import { EmployeePermission } from '@/lib/permissions';
import { Link } from '@inertiajs/react';
import { Money } from '@/renderers/cells/money';
import { StatusSwitch } from '@/renderers/cells/status-switch';
import { edit, toggle } from '@/routes/admin/employees';
import type { EmployeeRecord, ColumnDef } from '@/types';

export function createEmployeeGridSchema(): ColumnDef<EmployeeRecord>[] {
    return [
        {
            key: 'full_name',
            header: 'Employee',
            sortable: true,
            hideable: false,
            cell: (row) => (
                <Link href={edit(row.id).url} className="grid min-w-0 hover:underline">
                    <span className="truncate font-medium">{row.full_name}</span>
                    {row.employee_number && <span className="truncate text-xs text-muted-foreground">{row.employee_number}</span>}
                </Link>
            ),
            sortField: 'first_name',
        },
        { key: 'role_title', header: 'Role', sortable: true, cell: (row) => row.role_title ?? '—' },
        { key: 'employment_type_label', header: 'Type', cell: (row) => row.employment_type_label ?? '—', priorityLevel: 3 },
        {
            key: 'basic_monthly_salary',
            header: 'Monthly salary',
            sortable: true,
            cell: (row) => <Money value={row.basic_monthly_salary} />,
            priorityLevel: 2,
            align: 'right',
        },
        { key: 'hired_at', header: 'Hired', sortable: true, cell: (row) => <DateFormatted value={row.hired_at} />, priorityLevel: 3 },
        {
            key: 'is_active',
            header: 'Active',
            sortable: true,
            cell: (row) => (
                <StatusSwitch
                    checked={row.is_active}
                    field="is_active"
                    url={toggle(row.id).url}
                    permission={EmployeePermission.EmployeesEdit}
                    label={`Toggle ${row.first_name}`}
                />
            ),
            align: 'center',
        },
    ];
}
