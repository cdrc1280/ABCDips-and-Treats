import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/quotations';
import { createQuotationActionSchema, quotationActionSchema } from '@/schemas/quotation/actions';
import { quotationFilter, type QuotationSearchField } from '@/schemas/quotation/filter';
import { createQuotationGridSchema } from '@/schemas/quotation/grid';
import { quotationSummary } from '@/schemas/quotation/summary';
import type { QuotationRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<QuotationRecord>;
    filters: Filters<QuotationSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Quotations';

export default function QuotationIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<QuotationRecord, QuotationSearchField>({ url: index().url, records, filters, table });
    const rowActions = createQuotationActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={quotationActionSchema} />}>
                    Supplier quotes you can compare and convert into purchase orders.
                </PageHeader>

                <StatCards stats={summary} config={quotationSummary} count={3} />

                <FilterableDataTable<QuotationRecord, QuotationSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={quotationFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createQuotationGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No quotations match these filters."
                />
            </div>
        </>
    );
}

QuotationIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Production & Purchasing' }, { title }] };
