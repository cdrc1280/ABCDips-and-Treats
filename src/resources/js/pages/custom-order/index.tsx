import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/custom-orders';
import { createCustomOrderActionSchema, customOrderActionSchema } from '@/schemas/custom-order/actions';
import { customOrderFilter, type CustomOrderSearchField } from '@/schemas/custom-order/filter';
import { createCustomOrderGridSchema } from '@/schemas/custom-order/grid';
import { customOrderSummary } from '@/schemas/custom-order/summary';
import type { CustomOrderRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<CustomOrderRecord>;
    filters: Filters<CustomOrderSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Custom orders';

export default function CustomOrderIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<CustomOrderRecord, CustomOrderSearchField>({ url: index().url, records, filters, table });
    const rowActions = createCustomOrderActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={customOrderActionSchema} />}>
                    Custom cake inquiries — quote, schedule production and hand over.
                </PageHeader>

                <StatCards stats={summary} config={customOrderSummary} count={4} />

                <FilterableDataTable<CustomOrderRecord, CustomOrderSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={customOrderFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createCustomOrderGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No custom orders match these filters."
                />
            </div>
        </>
    );
}

CustomOrderIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title }] };
