import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/roles';
import { createRoleActionSchema, roleActionSchema } from '@/schemas/role/actions';
import { roleFilter, type RoleSearchField } from '@/schemas/role/filter';
import { createRoleGridSchema } from '@/schemas/role/grid';
import type { RoleRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<RoleRecord>;
    filters: Filters<RoleSearchField>;
    table: TableState;
}

const title = 'Roles';

export default function RoleIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<RoleRecord, RoleSearchField>({ url: index().url, records, filters, table });
    const rowActions = createRoleActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={roleActionSchema} />}>
                    Bundles of permissions you assign to staff accounts.
                </PageHeader>

                <FilterableDataTable<RoleRecord, RoleSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={roleFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createRoleGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No roles match these filters."
                />
            </div>
        </>
    );
}

RoleIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'System' }, { title }] };
