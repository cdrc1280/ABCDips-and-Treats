import { Head } from '@inertiajs/react';

import { FilterableDataTable } from '@/components/custom/filterable-data-table';
import { PageActions } from '@/components/custom/page-actions';
import { useDataTablePage } from '@/hooks/use-data-table-page';
import { ActionDropdown } from '@/renderers/cells/action-dropdown';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/blog-posts';
import { createBlogPostActionSchema, blogPostActionSchema } from '@/schemas/blog-post/actions';
import { blogPostFilter, type BlogPostSearchField } from '@/schemas/blog-post/filter';
import { createBlogPostGridSchema } from '@/schemas/blog-post/grid';
import type { BlogPostRecord, Filters, Paginated, TableState } from '@/types';
import { PageHeader } from '@/components/custom/page-header';

interface Props {
    records: Paginated<BlogPostRecord>;
    filters: Filters<BlogPostSearchField>;
    table: TableState;
}

const title = 'Blog posts';

export default function BlogPostIndex({ records, filters, table }: Props) {
    const page = useDataTablePage<BlogPostRecord, BlogPostSearchField>({ url: index().url, records, filters, table });
    const rowActions = createBlogPostActionSchema();

    return (
        <>
            <Head title={title} />
            <div className="grid gap-4">
                <PageHeader title={title} actions={<PageActions actions={blogPostActionSchema} />}>
                    Journal and vlog posts shown on the storefront blog.
                </PageHeader>

                <FilterableDataTable<BlogPostRecord, BlogPostSearchField>
                    {...page.savedState}
                    rowData={page.rowData}
                    meta={page.meta}
                    loading={page.loading}
                    filterFields={blogPostFilter}
                    filterValues={page.filterValues}
                    setFilterValues={page.setFilterValues}
                    applyFilters={() => page.applyFilters()}
                    resetFilters={page.resetFilters}
                    goToPage={page.goToPage}
                    setPerPage={page.setPerPage}
                    sort={page.currentSort}
                    onSort={page.handleSort}
                    columns={createBlogPostGridSchema()}
                    rowKey={(row) => row.id}
                    actions={(row) => <ActionDropdown row={row} actions={rowActions} />}
                    emptyMessage="No blog posts match these filters."
                />
            </div>
        </>
    );
}

BlogPostIndex.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Engagement' }, { title }] };
