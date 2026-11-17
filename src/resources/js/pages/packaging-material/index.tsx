import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/packaging-materials';
import { createPackagingMaterialActionSchema, packagingMaterialActionSchema } from '@/schemas/packaging-material/actions';
import { packagingMaterialFilter, type PackagingMaterialSearchField } from '@/schemas/packaging-material/filter';
import { createPackagingMaterialGridSchema } from '@/schemas/packaging-material/grid';
import { packagingMaterialSummary } from '@/schemas/packaging-material/summary';
import type { PackagingMaterialRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<PackagingMaterialRecord>;
    filters: Filters<PackagingMaterialSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Packaging';

export default function PackagingMaterialIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<PackagingMaterialRecord, PackagingMaterialSearchField>({ url: index().url, records, filters, table });
    const rowActions = createPackagingMaterialActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={packagingMaterialActionSchema} />}>
                    Boxes, bags, labels and other packaging used per order.
                </PageHeader>

                <StatCards stats={summary} config={packagingMaterialSummary} count={3} />

                <FilterableDataTable<PackagingMaterialRecord, PackagingMaterialSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={packagingMaterialFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createPackagingMaterialGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No packaging match these filters."
                />
            </div>
        </>
    );
}

PackagingMaterialIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Inventory' }, { title }] };
