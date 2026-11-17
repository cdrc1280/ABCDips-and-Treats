import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/attendances';
import { createAttendanceActionSchema, attendanceActionSchema } from '@/schemas/attendance/actions';
import { attendanceFilter, type AttendanceSearchField } from '@/schemas/attendance/filter';
import { createAttendanceGridSchema } from '@/schemas/attendance/grid';
import { attendanceSummary } from '@/schemas/attendance/summary';
import type { AttendanceRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<AttendanceRecord>;
    filters: Filters<AttendanceSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Attendance';

export default function AttendanceIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<AttendanceRecord, AttendanceSearchField>({ url: index().url, records, filters, table });
    const rowActions = createAttendanceActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={attendanceActionSchema} />}>
                    Daily time records, overtime and holidays — the input for payroll.
                </PageHeader>

                <StatCards stats={summary} config={attendanceSummary} count={3} />

                <FilterableDataTable<AttendanceRecord, AttendanceSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={attendanceFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createAttendanceGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No attendance match these filters."
                />
            </div>
        </>
    );
}

AttendanceIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'HR & Payroll' }, { title }] };
