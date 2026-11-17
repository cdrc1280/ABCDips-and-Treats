import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { FolderTree, Loader2, MoreVertical, Trash2 } from 'lucide-react';
import { useState, type FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { CreateHeader } from '@/components/custom/create-header';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Skeleton } from '@/components/ui/skeleton';
import { usePermissions } from '@/hooks/use-permissions';
import { tabGridClass } from '@/lib/layout';
import { CategoryPermission } from '@/lib/permissions';
import { destroy, edit, index, update } from '@/routes/admin/categories';
import type { CategoryRecord, Option } from '@/types';

import { CategoryForm, type CategoryFormData } from './category-form';
import { CategoryPreview } from './category-preview';
import { dashboard } from '@/routes/admin';

interface Props {
    record: CategoryRecord;
    switcher?: Option<string>[];
}

export default function CategoryEdit({ record, switcher }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<CategoryFormData>({
        name: record.name,
        slug: record.slug,
        description: record.description ?? '',
        sort_order: String(record.sort_order),
        is_active: record.is_active,
        image: null,
        remove_image: false,
    });

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        // Files can't travel in a PUT body: POST with method spoofing.
        form.transform((data) => ({ ...data, _method: 'put' }));
        form.post(update(record.id).url, { forceFormData: true, onSuccess: () => form.setDefaults() });
    };

    const remove = () =>
        router.delete(destroy(record.id).url, {
            onStart: () => setDeleting(true),
            onFinish: () => setDeleting(false),
            onSuccess: () => router.visit(index().url),
        });

    const headerActions = (
        <div className="flex items-center gap-2">
            <Deferred data="switcher" fallback={<Skeleton className="h-9 w-56" />}>
                <SelectDropdown<string>
                    className="w-56"
                    value={record.id}
                    options={switcher ?? []}
                    searchPlaceholder="Jump to category…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(CategoryPermission.CategoriesDelete) && !record.has_products && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete category
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.name}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Category list"
                    icon={FolderTree}
                    title={record.name}
                    description={`${record.products_count} ${record.products_count === 1 ? 'product' : 'products'} · /category/${record.slug}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <CategoryPreview data={form.data} currentImageUrl={record.image_url} productsCount={record.products_count} />
                    <CategoryForm data={form.data} setData={form.setData} errors={form.errors} isEdit currentImageUrl={record.image_url} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => router.visit(index().url)}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing || !form.isDirty} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save changes
                    </Button>
                </AppFooter>
            </form>
            <ConfirmDialog
                open={confirmDelete}
                onOpenChange={setConfirmDelete}
                title={`Delete ${record.name}?`}
                description="The category will be removed from the catalog."
                confirmLabel="Delete category"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

CategoryEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title: 'Categories', href: index().url }, { title: 'Edit' }],
};
