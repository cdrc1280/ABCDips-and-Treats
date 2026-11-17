import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/orders';
import { createOrderActionSchema, orderActionSchema } from '@/schemas/order/actions';
import { orderFilter, type OrderSearchField } from '@/schemas/order/filter';
import { createOrderGridSchema } from '@/schemas/order/grid';
import { orderSummary } from '@/schemas/order/summary';
import type { OrderRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<OrderRecord>;
    filters: Filters<OrderSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Orders';

export default function OrderIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<OrderRecord, OrderSearchField>({ url: index().url, records, filters, table });
    const rowActions = createOrderActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={orderActionSchema} />}>
                    Storefront and POS orders. Move them through the kitchen and out the door.
                </PageHeader>

                <StatCards stats={summary} config={orderSummary} count={4} />

                <FilterableDataTable<OrderRecord, OrderSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={orderFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createOrderGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No orders match these filters."
                />
            </div>
        </>
    );
}

OrderIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Orders & Sales' }, { title }] };
