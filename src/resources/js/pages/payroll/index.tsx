import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/payrolls';
import { createPayrollActionSchema, payrollActionSchema } from '@/schemas/payroll/actions';
import { payrollFilter, type PayrollSearchField } from '@/schemas/payroll/filter';
import { createPayrollGridSchema } from '@/schemas/payroll/grid';
import { payrollSummary } from '@/schemas/payroll/summary';
import type { PayrollRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<PayrollRecord>;
    filters: Filters<PayrollSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Payroll';

export default function PayrollIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<PayrollRecord, PayrollSearchField>({ url: index().url, records, filters, table });
    const rowActions = createPayrollActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={payrollActionSchema} />}>
                    Pay runs per period, generated from attendance. Adjust lines, approve, then mark paid.
                </PageHeader>

                <StatCards stats={summary} config={payrollSummary} count={3} />

                <FilterableDataTable<PayrollRecord, PayrollSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={payrollFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createPayrollGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No payroll match these filters."
                />
            </div>
        </>
    );
}

PayrollIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'HR & Payroll' }, { title }] };
