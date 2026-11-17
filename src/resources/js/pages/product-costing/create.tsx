import { Head, useForm } from '@inertiajs/react';
import { Calculator, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/product-costings';

import { ProductCostingForm, type ProductCostingFormData, type ProductCostingFormOptions } from './product-costing-form';
import { ProductCostingPreview } from './product-costing-preview';

const initial: ProductCostingFormData = {
    name: '',
    product_id: '',
    yield_qty: '1',
    yield_unit: 'tub (70g)',
    items: [],
    overhead_pct: '40',
    markup_pct: '66',
    labor_pct: '60',
};

export default function ProductCostingCreate({ product_options, ingredient_options, packaging_options }: ProductCostingFormOptions) {
    const form = useForm<ProductCostingFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New product costing" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Product costing list"
                    icon={Calculator}
                    title="New product costing"
                    description="Build a costing sheet — link a product or cost something new."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <ProductCostingPreview data={form.data} />
                    <ProductCostingForm
                        data={form.data}
                        setData={form.setData}
                        errors={form.errors}
                        options={{ product_options, ingredient_options, packaging_options }}
                    />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save product costing
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

ProductCostingCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Catalog' },
        { title: 'Product costing', href: index().url },
        { title: 'New product costing' },
    ],
};
