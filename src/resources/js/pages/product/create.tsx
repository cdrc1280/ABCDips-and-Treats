import { Head, useForm } from '@inertiajs/react';
import { Croissant, Loader2 } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { CreateHeader } from '@/components/custom/create-header';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { tabGridClass } from '@/lib/layout';
import { dashboard } from '@/routes/admin';
import { index, store } from '@/routes/admin/products';

import { ProductForm, type ProductFormData, type ProductFormOptions } from './product-form';
import { ProductPreview } from './product-preview';

const initial: ProductFormData = {
    name: '',
    slug: '',
    category_id: '',
    sku: '',
    barcode: '',
    short_description: '',
    description: '',
    tags: '',
    image_path: null,
    gallery: [],
    price: '',
    sale_price: '',
    sale_ends_at: '',
    stock_qty: '50',
    min_stock_qty: '10',
    inventory_mode: 'made_to_order',
    prep_time_minutes: '30',
    variation_type: 'none',
    variations: [],
    is_assorted: false,
    max_flavors: '',
    flavor: '',
    flavors: [],
    is_featured: false,
    is_best_seller: false,
    is_new_arrival: false,
    is_seasonal: false,
    is_limited: false,
    is_active: true,
    seo_title: '',
    seo_description: '',
    remove_image_path: false,
    keep_gallery: [],
};

export default function ProductCreate({ category_options }: ProductFormOptions) {
    const form = useForm<ProductFormData>(initial);

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.post(store().url, { forceFormData: true, onSuccess: () => form.setDefaults() });
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title="New product" />
            <form onSubmit={submit} className="grid gap-4">
                <CreateHeader
                    backHref={index().url}
                    backLabel="Product list"
                    icon={Croissant}
                    title="New product"
                    description="Add something new to the menu."
                    onClear={() => form.reset()}
                    clearDisabled={!form.isDirty}
                />
                <div className={tabGridClass}>
                    <ProductPreview data={form.data} />
                    <ProductForm data={form.data} setData={form.setData} errors={form.errors} options={{ category_options }} />
                </div>
                <AppFooter>
                    <Button type="button" variant="outline" size="sm" onClick={() => window.history.back()}>
                        Cancel
                    </Button>
                    <Button type="button" size="sm" disabled={form.processing} onClick={() => submit()}>
                        {form.processing && <Loader2 className="animate-spin" />} Save product
                    </Button>
                </AppFooter>
            </form>
        </UnsavedChangesGuard>
    );
}

ProductCreate.layout = {
    breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Catalog' }, { title: 'Products', href: index().url }, { title: 'New product' }],
};
