import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { CookingPot, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { ProductionBatchPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/production-batches';
import type { ProductionBatchRecord, Option } from '@/types';

import { ProductionBatchForm, type ProductionBatchFormData, type ProductionBatchFormOptions } from './production-batch-form';
import { ProductionBatchPreview } from './production-batch-preview';

type Props = { record: ProductionBatchRecord; switcher?: Option<string>[] } & ProductionBatchFormOptions;

export default function ProductionBatchEdit({ record, switcher, recipe_options, product_options, baker_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<ProductionBatchFormData>({
        batch_number: record.batch_number === null || record.batch_number === undefined ? '' : String(record.batch_number),
        recipe_id: record.recipe_id === null || record.recipe_id === undefined ? '' : String(record.recipe_id),
        product_id: record.product_id === null || record.product_id === undefined ? '' : String(record.product_id),
        planned_qty: record.planned_qty === null || record.planned_qty === undefined ? '' : String(record.planned_qty),
        actual_yield_qty: record.actual_yield_qty === null || record.actual_yield_qty === undefined ? '' : String(record.actual_yield_qty),
        status: record.status === null || record.status === undefined ? '' : String(record.status),
        baker_user_id: record.baker_user_id === null || record.baker_user_id === undefined ? '' : String(record.baker_user_id),
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
                    searchPlaceholder="Jump to production batch…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(ProductionBatchPermission.ProductionBatchesDelete) && !record.is_completed && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete production batch
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.batch_number}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Production batch list"
                    icon={CookingPot}
                    title={String(record.batch_number)}
                    description={''}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <ProductionBatchPreview data={form.data} title={String(record.batch_number)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <ProductionBatchForm
                            data={form.data}
                            setData={form.setData}
                            errors={form.errors}
                            options={{ recipe_options, product_options, baker_options }}
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
                title={`Delete ${record.batch_number}?`}
                description="This production batch will be removed."
                confirmLabel="Delete production batch"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

ProductionBatchEdit.layout = {
    breadcrumbs: [
        { title: 'Home', href: dashboard().url },
        { title: 'Production & Purchasing' },
        { title: 'Production batches', href: index().url },
        { title: 'Edit' },
    ],
};
