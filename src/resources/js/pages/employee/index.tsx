import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/employees';
import { createEmployeeActionSchema, employeeActionSchema } from '@/schemas/employee/actions';
import { employeeFilter, type EmployeeSearchField } from '@/schemas/employee/filter';
import { createEmployeeGridSchema } from '@/schemas/employee/grid';
import type { EmployeeRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<EmployeeRecord>;
    filters: Filters<EmployeeSearchField>;
    table: TableState;
}

const title = 'Employees';

export default function EmployeeIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<EmployeeRecord, EmployeeSearchField>({ url: index().url, records, filters, table });
    const rowActions = createEmployeeActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={employeeActionSchema} />}>
                    Staff records, roles and monthly salary used by payroll.
                </PageHeader>

                <FilterableDataTable<EmployeeRecord, EmployeeSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={employeeFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createEmployeeGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No employees match these filters."
                />
            </div>
        </>
    );
}

EmployeeIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'HR & Payroll' }, { title }] };
