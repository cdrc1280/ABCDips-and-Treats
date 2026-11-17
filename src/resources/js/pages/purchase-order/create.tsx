import { Head, useForm } from '@inertiajs/react';
import { ClipboardList, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/purchase-orders';

import { PurchaseOrderForm, type PurchaseOrderFormData, type PurchaseOrderFormOptions } from './purchase-order-form';
import { PurchaseOrderPreview } from './purchase-order-preview';
import { PoPdfImport } from './po-pdf-import';

const initial: PurchaseOrderFormData = {
    po_number: '',
    supplier_id: '',
    status: 'draft',
    expected_delivery_date: '',
    payment_terms: '',
    items: [],
    tax: '0',
    delivery_receipt_no: '',
    sales_invoice_no: '',
    notes: '',
};

export default function PurchaseOrderCreate({ supplier_options, ingredient_options }: PurchaseOrderFormOptions) {
    const form = useForm<PurchaseOrderFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New purchase order" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Purchase order list"
                    icon={ClipboardList}
                    title="New purchase order"
                    description="Order ingredients from a supplier. Stock changes only when the PO is received."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <PurchaseOrderPreview data={form.data} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <PoPdfImport setData={form.setData} />
                        <PurchaseOrderForm
                            data={form.data}
                            setData={form.setData}
                            errors={form.errors}
                            options={{ supplier_options, ingredient_options }}
                        />
                    </div>
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save purchase order
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

PurchaseOrderCreate.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Production & Purchasing' },
        { title: 'Purchase orders', href: index().url },
        { title: 'New purchase order' },
    ],
};
