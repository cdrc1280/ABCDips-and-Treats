import { Head, useForm } from '@inertiajs/react';
import { FileSpreadsheet, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/quotations';

import { QuotationForm, type QuotationFormData, type QuotationFormOptions } from './quotation-form';
import { QuotationPreview } from './quotation-preview';

const initial: QuotationFormData = {
    quotation_number: '',
    supplier_id: '',
    client_name: '',
    status: 'draft',
    quotation_date: '',
    valid_until: '',
    items: [],
    tax: '0',
    notes: '',
};

export default function QuotationCreate({ supplier_options, ingredient_options }: QuotationFormOptions) {
    const form = useForm<QuotationFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New quotation" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Quotation list"
                    icon={FileSpreadsheet}
                    title="New quotation"
                    description="Record a supplier quote. Convert it to a PO once accepted."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <QuotationPreview data={form.data} />
                    <QuotationForm data={form.data} setData={form.setData} errors={form.errors} options={{ supplier_options, ingredient_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save quotation
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

QuotationCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Production & Purchasing' },
        { title: 'Quotations', href: index().url },
        { title: 'New quotation' },
    ],
};
