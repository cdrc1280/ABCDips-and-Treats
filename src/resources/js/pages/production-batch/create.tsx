import { Head, useForm } from '@inertiajs/react';
import { CookingPot, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/production-batches';

import { ProductionBatchForm, type ProductionBatchFormData, type ProductionBatchFormOptions } from './production-batch-form';
import { ProductionBatchPreview } from './production-batch-preview';

const initial: ProductionBatchFormData = {
    batch_number: '',
    recipe_id: '',
    product_id: '',
    planned_qty: '1',
    actual_yield_qty: '',
    status: 'planned',
    baker_user_id: '',
    notes: '',
};

export default function ProductionBatchCreate({ recipe_options, product_options, baker_options }: ProductionBatchFormOptions) {
    const form = useForm<ProductionBatchFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New production batch" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Production batch list"
                    icon={CookingPot}
                    title="New production batch"
                    description="Plan a bake from a recipe. Stock moves only when the batch is completed."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <ProductionBatchPreview data={form.data} />
                    <ProductionBatchForm
                        data={form.data}
                        setData={form.setData}
                        errors={form.errors}
                        options={{ recipe_options, product_options, baker_options }}
                    />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save production batch
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

ProductionBatchCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Production & Purchasing' },
        { title: 'Production batches', href: index().url },
        { title: 'New production batch' },
    ],
};
