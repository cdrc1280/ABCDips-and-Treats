import { Head, useForm } from '@inertiajs/react';
import { PackageOpen, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/packaging-materials';

import { PackagingMaterialForm, type PackagingMaterialFormData } from './packaging-material-form';
import { PackagingMaterialPreview } from './packaging-material-preview';

const initial: PackagingMaterialFormData = {
    name: '',
    sku: '',
    type: 'box',
    unit: 'pcs',
    cost_per_unit: '0',
    stock_qty: '0',
    min_stock_qty: '10',
    is_active: true,
    notes: '',
};

export default function PackagingMaterialCreate() {
    const form = useForm<PackagingMaterialFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New packaging material" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Packaging material list"
                    icon={PackageOpen}
                    title="New packaging material"
                    description="Add a box, bag, label or other packaging item."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <PackagingMaterialPreview data={form.data} />
                    <PackagingMaterialForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save packaging material
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

PackagingMaterialCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Inventory' },
        { title: 'Packaging', href: index().url },
        { title: 'New packaging material' },
    ],
};
