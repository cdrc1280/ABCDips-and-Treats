import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/production-batches';
import { createProductionBatchActionSchema, productionBatchActionSchema } from '@/schemas/production-batch/actions';
import { productionBatchFilter, type ProductionBatchSearchField } from '@/schemas/production-batch/filter';
import { createProductionBatchGridSchema } from '@/schemas/production-batch/grid';
import { productionBatchSummary } from '@/schemas/production-batch/summary';
import type { ProductionBatchRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<ProductionBatchRecord>;
    filters: Filters<ProductionBatchSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Production batches';

export default function ProductionBatchIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<ProductionBatchRecord, ProductionBatchSearchField>({ url: index().url, records, filters, table });
    const rowActions = createProductionBatchActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={productionBatchActionSchema} />}>
                    Plan and complete bakes. Completing a batch deducts ingredients and adds finished stock.
                </PageHeader>

                <StatCards stats={summary} config={productionBatchSummary} count={3} />

                <FilterableDataTable<ProductionBatchRecord, ProductionBatchSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={productionBatchFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createProductionBatchGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No production batches match these filters."
                />
            </div>
        </>
    );
}

ProductionBatchIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Production & Purchasing' }, { title }] };
