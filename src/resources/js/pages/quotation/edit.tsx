import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { FileSpreadsheet, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { QuotationPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/quotations';
import type { QuotationRecord, Option } from '@/types';

import { QuotationForm, type QuotationFormData, type QuotationFormOptions } from './quotation-form';
import { QuotationPreview } from './quotation-preview';

type Props = { record: QuotationRecord; switcher?: Option<string>[] } & QuotationFormOptions;

export default function QuotationEdit({ record, switcher, supplier_options, ingredient_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<QuotationFormData>({
        quotation_number: record.quotation_number === null || record.quotation_number === undefined ? '' : String(record.quotation_number),
        supplier_id: record.supplier_id === null || record.supplier_id === undefined ? '' : String(record.supplier_id),
        client_name: record.client_name === null || record.client_name === undefined ? '' : String(record.client_name),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        quotation_date: record.quotation_date === null || record.quotation_date === undefined ? '' : String(record.quotation_date),
        valid_until: record.valid_until === null || record.valid_until === undefined ? '' : String(record.valid_until),
        items: record.items.map((row) => ({
            ingredient_id: row.ingredient_id === null || row.ingredient_id === undefined ? '' : String(row.ingredient_id),
            item_description: row.item_description === null || row.item_description === undefined ? '' : String(row.item_description),
            qty: row.qty === null || row.qty === undefined ? '' : String(row.qty),
            unit: row.unit === null || row.unit === undefined ? '' : String(row.unit),
            unit_price: row.unit_price === null || row.unit_price === undefined ? '' : String(row.unit_price),
        })),
        tax: record.tax === null || record.tax === undefined ? '' : String(record.tax),
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
                    searchPlaceholder="Jump to quotation…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(QuotationPermission.QuotationsDelete) && !record.is_converted && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete quotation
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.quotation_number}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Quotation list"
                    icon={FileSpreadsheet}
                    title={String(record.quotation_number)}
                    description={`${record.supplier_name ?? 'No supplier'} · ₱${record.total.toFixed(2)}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <QuotationPreview data={form.data} title={String(record.quotation_number)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <QuotationForm
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
                title={`Delete ${record.quotation_number}?`}
                description="This quotation will be removed."
                confirmLabel="Delete quotation"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

QuotationEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Production & Purchasing' },
        { title: 'Quotations', href: index().url },
        { title: 'Edit' },
    ],
};
