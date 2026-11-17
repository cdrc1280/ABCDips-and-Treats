import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Calculator, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { ProductCostingPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/product-costings';
import type { ProductCostingRecord, Option } from '@/types';

import { ProductCostingForm, type ProductCostingFormData, type ProductCostingFormOptions } from './product-costing-form';
import { ProductCostingPreview } from './product-costing-preview';

type Props = { record: ProductCostingRecord; switcher?: Option<string>[] } & ProductCostingFormOptions;

export default function ProductCostingEdit({ record, switcher, product_options, ingredient_options, packaging_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<ProductCostingFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        product_id: record.product_id === null || record.product_id === undefined ? '' : String(record.product_id),
        yield_qty: record.yield_qty === null || record.yield_qty === undefined ? '' : String(record.yield_qty),
        yield_unit: record.yield_unit === null || record.yield_unit === undefined ? '' : String(record.yield_unit),
        items: record.items.map((row) => ({
            group: row.group === null || row.group === undefined ? '' : String(row.group),
            ingredient_id: row.ingredient_id === null || row.ingredient_id === undefined ? '' : String(row.ingredient_id),
            packaging_material_id:
                row.packaging_material_id === null || row.packaging_material_id === undefined ? '' : String(row.packaging_material_id),
            name: row.name === null || row.name === undefined ? '' : String(row.name),
            unit: row.unit === null || row.unit === undefined ? '' : String(row.unit),
            package_amount: row.package_amount === null || row.package_amount === undefined ? '' : String(row.package_amount),
            package_price: row.package_price === null || row.package_price === undefined ? '' : String(row.package_price),
            qty_used: row.qty_used === null || row.qty_used === undefined ? '' : String(row.qty_used),
        })),
        overhead_pct: record.overhead_pct === null || record.overhead_pct === undefined ? '' : String(record.overhead_pct),
        markup_pct: record.markup_pct === null || record.markup_pct === undefined ? '' : String(record.markup_pct),
        labor_pct: record.labor_pct === null || record.labor_pct === undefined ? '' : String(record.labor_pct),
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
                    searchPlaceholder="Jump to product costing…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(ProductCostingPermission.ProductCostingsDelete) && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete product costing
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
                    backLabel="Product costing list"
                    icon={Calculator}
                    title={String(record.name)}
                    description={`Cost ₱${record.total_cost.toFixed(2)} · sells ₱${record.selling_price.toFixed(2)} · ₱${record.price_per_piece.toFixed(2)} each`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <ProductCostingPreview data={form.data} title={String(record.name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <ProductCostingForm
                            data={form.data}
                            setData={form.setData}
                            errors={form.errors}
                            options={{ product_options, ingredient_options, packaging_options }}
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
                title={`Delete ${record.name}?`}
                description="This product costing will be removed."
                confirmLabel="Delete product costing"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

ProductCostingEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title: 'Product costing', href: index().url }, { title: 'Edit' }],
};
