import { Deferred, Head, router, useForm } from '@inertiajs/react';
import { Croissant, Loader2, MoreVertical, Trash2 } from 'lucide-react';
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
import { ProductPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { destroy, edit, index, update } from '@/routes/admin/products';
import type { ProductRecord, Option } from '@/types';

import { ProductForm, type ProductFormData, type ProductFormOptions } from './product-form';
import { ProductPreview } from './product-preview';

type Props = { record: ProductRecord; switcher?: Option<string>[] } & ProductFormOptions;

export default function ProductEdit({ record, switcher, category_options }: Props) {
    const { can } = usePermissions();
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const form = useForm<ProductFormData>({
        name: record.name === null || record.name === undefined ? '' : String(record.name),
        slug: record.slug === null || record.slug === undefined ? '' : String(record.slug),
        category_id: record.category_id === null || record.category_id === undefined ? '' : String(record.category_id),
        sku: record.sku === null || record.sku === undefined ? '' : String(record.sku),
        barcode: record.barcode === null || record.barcode === undefined ? '' : String(record.barcode),
        short_description: record.short_description === null || record.short_description === undefined ? '' : String(record.short_description),
        description: record.description === null || record.description === undefined ? '' : String(record.description),
        tags: record.tags.join(', '),
        image_path: null,
        gallery: [],
        price: record.price === null || record.price === undefined ? '' : String(record.price),
        sale_price: record.sale_price === null || record.sale_price === undefined ? '' : String(record.sale_price),
        sale_ends_at: record.sale_ends_at ? record.sale_ends_at.slice(0, 16) : '',
        stock_qty: record.stock_qty === null || record.stock_qty === undefined ? '' : String(record.stock_qty),
        min_stock_qty: record.min_stock_qty === null || record.min_stock_qty === undefined ? '' : String(record.min_stock_qty),
        inventory_mode: record.inventory_mode,
        prep_time_minutes: record.prep_time_minutes === null || record.prep_time_minutes === undefined ? '' : String(record.prep_time_minutes),
        variation_type: record.variation_type === null || record.variation_type === undefined ? '' : String(record.variation_type),
        variations: record.variations.map((row) => ({
            label: row.label === null || row.label === undefined ? '' : String(row.label),
            price_modifier: row.price_modifier === null || row.price_modifier === undefined ? '' : String(row.price_modifier),
        })),
        is_assorted: record.is_assorted,
        max_flavors: record.max_flavors === null || record.max_flavors === undefined ? '' : String(record.max_flavors),
        flavor: record.flavor === null || record.flavor === undefined ? '' : String(record.flavor),
        flavors: record.flavors.map((row) => ({
            name: row.name === null || row.name === undefined ? '' : String(row.name),
            price_modifier: row.price_modifier === null || row.price_modifier === undefined ? '' : String(row.price_modifier),
        })),
        is_featured: record.is_featured,
        is_best_seller: record.is_best_seller,
        is_new_arrival: record.is_new_arrival,
        is_seasonal: record.is_seasonal,
        is_limited: record.is_limited,
        is_active: record.is_active,
        seo_title: record.seo_title === null || record.seo_title === undefined ? '' : String(record.seo_title),
        seo_description: record.seo_description === null || record.seo_description === undefined ? '' : String(record.seo_description),
        remove_image_path: false,
        keep_gallery: record.gallery,
    });

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.transform((data) => ({ ...data, _method: 'put' }));
        form.post(update(record.id).url, { forceFormData: true, onSuccess: () => form.setDefaults() });
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
                    searchPlaceholder="Jump to product…"
                    onChange={(id) => id && id !== record.id && router.visit(edit(id).url)}
                />
            </Deferred>
            {can(ProductPermission.ProductsDelete) && !record.has_orders && (
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="outline" size="icon" aria-label="More actions">
                            <MoreVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem variant="destructive" onSelect={() => setConfirmDelete(true)}>
                            <Trash2 /> Delete product
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
                    backLabel="Product list"
                    icon={Croissant}
                    title={String(record.name)}
                    description={`${record.sku} · ${record.category_name ?? 'No category'}`}
                    actions={headerActions}
                />
                <div className={tabGridClass}>
                    <ProductPreview data={form.data} title={String(record.name)} />
                    <div className="grid min-w-0 grid-cols-[minmax(0,1fr)] content-start gap-4">
                        <ProductForm
                            data={form.data}
                            setData={form.setData}
                            errors={form.errors}
                            options={{ category_options }}
                            currentUrls={{
                                image_path: record.image_path_url,
                                gallery_list: record.gallery.map((path, i) => ({ path, url: record.gallery_urls[i] })),
                            }}
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
                description="This product will be removed."
                confirmLabel="Delete product"
                processing={deleting}
                onConfirm={remove}
            />
        </UnsavedChangesGuard>
    );
}

ProductEdit.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title: 'Products', href: index().url }, { title: 'Edit' }],
};
