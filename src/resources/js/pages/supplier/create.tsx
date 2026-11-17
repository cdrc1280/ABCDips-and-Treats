import { Head, useForm } from '@inertiajs/react';
import { Truck, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/suppliers';

import { SupplierForm, type SupplierFormData } from './supplier-form';
import { SupplierPreview } from './supplier-preview';

const initial: SupplierFormData = {
    name: '',
    contact_person: '',
    email: '',
    phone: '',
    payment_terms: 'Net 30',
    address: '',
    notes: '',
};

export default function SupplierCreate() {
    const form = useForm<SupplierFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New supplier" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Supplier list"
                    icon={Truck}
                    title="New supplier"
                    description="Add a vendor for ingredients or packaging."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <SupplierPreview data={form.data} />
                    <SupplierForm data={form.data} setData={form.setData} errors={form.errors} options={{}} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save supplier
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

SupplierCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Inventory' },
        { title: 'Suppliers', href: index().url },
        { title: 'New supplier' },
    ],
};
