import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { StatCards } from '@/components/custom/stat-cards';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/suggestions';
import { createSuggestionActionSchema, suggestionActionSchema } from '@/schemas/suggestion/actions';
import { suggestionFilter, type SuggestionSearchField } from '@/schemas/suggestion/filter';
import { createSuggestionGridSchema } from '@/schemas/suggestion/grid';
import { suggestionSummary } from '@/schemas/suggestion/summary';
import type { SuggestionRecord, Filters, Paginated, StatData, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<SuggestionRecord>;
    filters: Filters<SuggestionSearchField>;
    table: TableState;
    summary?: StatData[];
}

const title = 'Suggestions';

export default function SuggestionIndex({ records, filters, table, summary }: Props) {
    const page = useDataTablePage<SuggestionRecord, SuggestionSearchField>({ url: index().url, records, filters, table });
    const rowActions = createSuggestionActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={suggestionActionSchema} />}>
                    Product ideas and feedback customers send from the storefront.
                </PageHeader>

                <StatCards stats={summary} config={suggestionSummary} count={3} />

                <FilterableDataTable<SuggestionRecord, SuggestionSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={suggestionFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createSuggestionGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No suggestions match these filters."
                />
            </div>
        </>
    );
}

SuggestionIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Engagement' }, { title }] };
