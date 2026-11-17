import { Head, useForm } from '@inertiajs/react';
import { FolderTree, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { index, store } from '@/routes/admin/categories';

import { CategoryForm, type CategoryFormData } from './category-form';
import { CategoryPreview } from './category-preview';
import { dashboard } from '@/routes/admin';

const initial: CategoryFormData = { name: '', slug: '', description: '', sort_order: '', is_active: true, image: null, remove_image: false };

export default function CategoryCreate() {
    const form = useForm<CategoryFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { forceFormData: true, onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New category" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Category list"
                    icon={FolderTree}
                    title="New category"
                    description="Add a section to the storefront catalog."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <CategoryPreview data={form.data} />
                    <CategoryForm data={form.data} setData={form.setData} errors={form.errors} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save category
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

CategoryCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Catalog' },
        { title: 'Categories', href: index().url },
        { title: 'New category' },
    ],
};
