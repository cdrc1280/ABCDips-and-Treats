import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/users';
import { createUserActionSchema, userActionSchema } from '@/schemas/user/actions';
import { userFilter, type UserSearchField } from '@/schemas/user/filter';
import { createUserGridSchema } from '@/schemas/user/grid';
import type { UserRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<UserRecord>;
    filters: Filters<UserSearchField>;
    table: TableState;
}

const title = 'Staff accounts';

export default function UserIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<UserRecord, UserSearchField>({ url: index().url, records, filters, table });
    const rowActions = createUserActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={userActionSchema} />}>
                    People who sign in to the back office, and what their roles let them do.
                </PageHeader>

                <FilterableDataTable<UserRecord, UserSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={userFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createUserGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No staff accounts match these filters."
                />
            </div>
        </>
    );
}

UserIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'System' }, { title }] };
