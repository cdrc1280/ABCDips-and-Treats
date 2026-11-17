import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { ClipboardList, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { PurchaseOrderPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/purchase-orders';
import type { PurchaseOrderRecord, Option } from '@/types';

import { PurchaseOrderForm, type PurchaseOrderFormData, type PurchaseOrderFormOptions } from './purchase-order-form';
import { PurchaseOrderPreview } from './purchase-order-preview';

type Props = { record: PurchaseOrderRecord; switcher?: Option<string>[] } & PurchaseOrderFormOptions;

export default function PurchaseOrderEdit({ record, switcher, supplier_options, ingredient_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<PurchaseOrderFormData>({
        po_number: record.po_number === null || record.po_number === undefined ? '' : String(record.po_number),
        supplier_id: record.supplier_id === null || record.supplier_id === undefined ? '' : String(record.supplier_id),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        expected_delivery_date:
            record.expected_delivery_date === null || record.expected_delivery_date === undefined ? '' : String(record.expected_delivery_date),
        payment_terms: record.payment_terms === null || record.payment_terms === undefined ? '' : String(record.payment_terms),
        items: record.items.map((row) => ({
            ingredient_id: row.ingredient_id === null || row.ingredient_id === undefined ? '' : String(row.ingredient_id),
            qty_ordered: row.qty_ordered === null || row.qty_ordered === undefined ? '' : String(row.qty_ordered),
            unit_cost: row.unit_cost === null || row.unit_cost === undefined ? '' : String(row.unit_cost),
        })),
        tax: record.tax === null || record.tax === undefined ? '' : String(record.tax),
        delivery_receipt_no:
            record.delivery_receipt_no === null || record.delivery_receipt_no === undefined ? '' : String(record.delivery_receipt_no),
        sales_invoice_no: record.sales_invoice_no === null || record.sales_invoice_no === undefined ? '' : String(record.sales_invoice_no),
        notes: record.notes === null || record.notes === undefined ? '' : String(record.notes),
    });

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.put(update(record.id).url, { onSuccess: () => form.setDefaults() });
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
                    searchPlaceholder="Jump to purchase order…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(PurchaseOrderPermission.PurchaseOrdersDelete) && !(record.status === 'received') && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete purchase order
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.po_number}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Purchase order list"
                    icon={ClipboardList}
                    title={String(record.po_number)}
                    description={`${record.supplier_name ?? ''} · ₱${record.total.toFixed(2)}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <PurchaseOrderPreview data={form.data} title={String(record.po_number)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <PurchaseOrderForm
                            data={form.data}
                            setData={form.setData}
                            errors={form.errors}
                            options={{ supplier_options, ingredient_options }}
                        />
                    </div>
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
                title={`Delete ${record.po_number}?`}
                description="This purchase order will be removed."
                confirmLabel="Delete purchase order"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

PurchaseOrderEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Production & Purchasing' },
        { title: 'Purchase orders', href: index().url },
        { title: 'Edit' },
    ],
};
