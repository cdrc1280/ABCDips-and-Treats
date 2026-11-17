import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Wheat, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { IngredientPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/ingredients';
import type { IngredientRecord, Option } from '@/types';

import { IngredientForm, type IngredientFormData, type IngredientFormOptions } from './ingredient-form';
import { IngredientPreview } from './ingredient-preview';

type Props = { record: IngredientRecord; switcher?: Option<string>[] } & IngredientFormOptions;

export default function IngredientEdit({ record, switcher, supplier_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<IngredientFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        sku: record.sku === null || record.sku === undefined ? '' : String(record.sku),
        supplier_id: record.supplier_id === null || record.supplier_id === undefined ? '' : String(record.supplier_id),
        unit: record.unit === null || record.unit === undefined ? '' : String(record.unit),
        item_unit: record.item_unit === null || record.item_unit === undefined ? '' : String(record.item_unit),
        item_price: record.item_price === null || record.item_price === undefined ? '' : String(record.item_price),
        stock_qty: record.stock_qty === null || record.stock_qty === undefined ? '' : String(record.stock_qty),
        min_stock_qty: record.min_stock_qty === null || record.min_stock_qty === undefined ? '' : String(record.min_stock_qty),
        reorder_qty: record.reorder_qty === null || record.reorder_qty === undefined ? '' : String(record.reorder_qty),
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
                    searchPlaceholder="Jump to ingredient…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(IngredientPermission.IngredientsDelete) && !(record.used_in_recipes > 0) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete ingredient
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            )}
        </div>
    );

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={`Edit ${record.name}`} />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Ingredient list"
                    icon={Wheat}
                    title={String(record.name)}
                    description={`${record.sku} · ${record.used_in_recipes} recipes`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <IngredientPreview data={form.data} title={String(record.name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <IngredientForm data={form.data} setData={form.setData} errors={form.errors} options={{ supplier_options }} />
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
                title={`Delete ${record.name}?`}
                description="This ingredient will be removed."
                confirmLabel="Delete ingredient"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

IngredientEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Inventory' }, { title: 'Ingredients', href: index().url }, { title: 'Edit' }],
};
