import { Head, useForm } from '@inertiajs/react';
import { ChefHat, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/recipes';

import { RecipeForm, type RecipeFormData, type RecipeFormOptions } from './recipe-form';
import { RecipePreview } from './recipe-preview';

const initial: RecipeFormData = {
    name: '',
    product_id: '',
    yield_qty: '1',
    prep_time_minutes: '20',
    baking_time_minutes: '40',
    instructions: '',
    ingredients: [],
    packaging: [],
    overhead_pct: '40',
    markup_pct: '66',
    labor_pct: '60',
};

export default function RecipeCreate({ product_options, ingredient_options, packaging_options }: RecipeFormOptions) {
    const form = useForm<RecipeFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New recipe" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Recipe list"
                    icon={ChefHat}
                    title="New recipe"
                    description="List what one batch needs. Costs update from current ingredient prices."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <RecipePreview data={form.data} />
                    <RecipeForm
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
                        {form.processing && <Loader2 className="animate-spin" />} Save recipe
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

RecipeCreate.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title: 'Recipes', href: index().url }, { title: 'New recipe' }],
};
