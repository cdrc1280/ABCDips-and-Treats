import { Head, useForm } from '@inertiajs/react';
import { Wheat, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/ingredients';

import { IngredientForm, type IngredientFormData, type IngredientFormOptions } from './ingredient-form';
import { IngredientPreview } from './ingredient-preview';

const initial: IngredientFormData = {
    name: '',
    sku: '',
    supplier_id: '',
    unit: 'grams',
    item_unit: '1000',
    item_price: '0',
    stock_qty: '0',
    min_stock_qty: '100',
    reorder_qty: '1000',
};

export default function IngredientCreate({ supplier_options }: IngredientFormOptions) {
    const form = useForm<IngredientFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New ingredient" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Ingredient list"
                    icon={Wheat}
                    title="New ingredient"
                    description="Track a raw material, its pack price and reorder point."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <IngredientPreview data={form.data} />
                    <IngredientForm data={form.data} setData={form.setData} errors={form.errors} options={{ supplier_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save ingredient
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

IngredientCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Inventory' },
        { title: 'Ingredients', href: index().url },
        { title: 'New ingredient' },
    ],
};
