import { DateTimePicker } from '@/components/custom/date-picker';
import { Croissant, Eye, Images, Layers, Tag } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface ProductFormData {
    name: string;
    slug: string;
    category_id: string;
    sku: string;
    barcode: string;
    short_description: string;
    description: string;
    tags: string;
    image_path: File | null;
    gallery: File[];
    price: string;
    sale_price: string;
    sale_ends_at: string;
    stock_qty: string;
    min_stock_qty: string;
    inventory_mode: string;
    prep_time_minutes: string;
    variation_type: string;
    variations: Record<string, string>[];
    is_assorted: boolean;
    max_flavors: string;
    flavor: string;
    flavors: Record<string, string>[];
    is_featured: boolean;
    is_best_seller: boolean;
    is_new_arrival: boolean;
    is_seasonal: boolean;
    is_limited: boolean;
    is_active: boolean;
    seo_title: string;
    seo_description: string;
    remove_image_path: boolean;
    keep_gallery: string[];
}

export type ProductFormOptions = { category_options?: Option<string>[] };

interface Props {
    data: ProductFormData;
    setData: <K extends keyof ProductFormData>(key: K, value: ProductFormData[K]) => void;
    errors: Partial<Record<keyof ProductFormData, string>>;
    options: ProductFormOptions;
    currentUrls?: { image_path?: string | null; gallery_list?: { path: string; url: string }[] };
}

const inventoryModeOptions = [
    { value: 'made_to_order', label: 'Made to order', description: 'Ingredients are used when it sells' },
    { value: 'baked_ahead', label: 'Baked ahead', description: 'Ingredients are used by production batches' },
];

const variationTypeOptions = [
    { value: 'none', label: 'No variations' },
    { value: 'assorted', label: 'Assorted (multi-flavor box)' },
    { value: 'weight', label: 'Weight / grams' },
    { value: 'pieces', label: 'Pieces' },
    { value: 'size', label: 'Size' },
    { value: 'packaging', label: 'Packaging' },
    { value: 'custom', label: 'Custom' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function ProductForm({ data, setData, errors, options, currentUrls }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={Croissant}
                title="Details"
                description="Name, category and description."
                fields={['name', 'slug', 'category_id', 'sku', 'barcode', 'short_description', 'description', 'tags']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Name</FieldLabel>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            aria-invalid={!!errors.name}
                            onChange={(event) => setData('name', event.target.value)}
                            placeholder="e.g. Classic Banana Bread Loaf"
                        />
                        <InputError message={errors.name} />
                    </Field>
                    <Field>
                        <FieldLabel name="slug">URL slug</FieldLabel>
                        <Input
                            id="slug"
                            type="text"
                            value={data.slug}
                            aria-invalid={!!errors.slug}
                            onChange={(event) => setData('slug', event.target.value)}
                        />
                        <FieldDescription>Leave empty to generate from the name.</FieldDescription>
                        <InputError message={errors.slug} />
                    </Field>
                    <Field>
                        <FieldLabel name="category_id">Category</FieldLabel>
                        <SelectDropdown<string>
                            id="category_id"
                            value={data.category_id ? String(data.category_id) : null}
                            options={options.category_options ?? []}
                            invalid={!!errors.category_id}
                            onChange={(value) => setData('category_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.category_id} />
                    </Field>
                    <Field>
                        <FieldLabel name="sku">SKU</FieldLabel>
                        <Input
                            id="sku"
                            type="text"
                            value={data.sku}
                            aria-invalid={!!errors.sku}
                            onChange={(event) => setData('sku', event.target.value)}
                        />
                        <InputError message={errors.sku} />
                    </Field>
                    <Field>
                        <FieldLabel name="barcode">Barcode</FieldLabel>
                        <Input
                            id="barcode"
                            type="text"
                            value={data.barcode}
                            aria-invalid={!!errors.barcode}
                            onChange={(event) => setData('barcode', event.target.value)}
                        />
                        <FieldDescription>Scanned at the POS.</FieldDescription>
                        <InputError message={errors.barcode} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="short_description">Short description</FieldLabel>
                        <Textarea
                            id="short_description"
                            rows={3}
                            value={data.short_description}
                            aria-invalid={!!errors.short_description}
                            onChange={(event) => setData('short_description', event.target.value)}
                        />
                        <InputError message={errors.short_description} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="description">Full description</FieldLabel>
                        <Textarea
                            id="description"
                            rows={10}
                            value={data.description}
                            aria-invalid={!!errors.description}
                            onChange={(event) => setData('description', event.target.value)}
                        />
                        <InputError message={errors.description} />
                    </Field>
                    <Field>
                        <FieldLabel name="tags">Tags</FieldLabel>
                        <Input
                            id="tags"
                            value={data.tags}
                            aria-invalid={!!errors.tags}
                            onChange={(event) => setData('tags', event.target.value)}
                            placeholder="e.g. bestseller, gift, chocolate"
                        />
                        <FieldDescription>Separate tags with commas.</FieldDescription>
                        <InputError message={errors.tags} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={Images} title="Photos" description="Main photo and gallery." fields={['image_path', 'gallery']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="image_path">Main photo</FieldLabel>
                        {currentUrls?.image_path && !data.remove_image_path && !data.image_path && (
                            <div className="flex items-center gap-3">
                                <img src={currentUrls.image_path} alt="" className="size-16 rounded-lg border object-cover" />
                                <Button type="button" variant="outline" size="sm" onClick={() => setData('remove_image_path', true)}>
                                    Remove
                                </Button>
                            </div>
                        )}
                        <Input
                            id="image_path"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            aria-invalid={!!errors.image_path}
                            onChange={(event) => {
                                setData('image_path', event.target.files?.[0] ?? null);
                                setData('remove_image_path', false);
                            }}
                        />
                        <InputError message={errors.image_path} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="gallery">Gallery</FieldLabel>
                        {(currentUrls?.gallery_list ?? []).length > 0 && (
                            <div className="flex flex-wrap gap-3">
                                {(currentUrls?.gallery_list ?? []).map((item) => {
                                    const kept = data.keep_gallery.includes(item.path);
                                    return (
                                        <button
                                            key={item.path}
                                            type="button"
                                            onClick={() =>
                                                setData(
                                                    'keep_gallery',
                                                    kept ? data.keep_gallery.filter((p) => p !== item.path) : [...data.keep_gallery, item.path],
                                                )
                                            }
                                            className="relative rounded-lg border focus-visible:ring-2 focus-visible:ring-ring"
                                            aria-pressed={!kept}
                                            aria-label={kept ? 'Remove image' : 'Keep image'}
                                        >
                                            <img
                                                src={item.url}
                                                alt=""
                                                className={`size-20 rounded-lg object-cover ${kept ? '' : 'opacity-30 grayscale'}`}
                                            />
                                            {!kept && (
                                                <span className="absolute inset-0 flex items-center justify-center text-xs font-medium">Removed</span>
                                            )}
                                        </button>
                                    );
                                })}
                            </div>
                        )}
                        <Input
                            id="gallery"
                            type="file"
                            multiple
                            accept="image/jpeg,image/png,image/webp"
                            onChange={(event) => setData('gallery', Array.from(event.target.files ?? []))}
                        />
                        <FieldDescription>Click a current image to remove it. New files are added to the end.</FieldDescription>
                        <InputError message={errors.gallery} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Tag}
                title="Pricing & stock"
                description="Price, sale and stock levels."
                fields={['price', 'sale_price', 'sale_ends_at', 'stock_qty', 'min_stock_qty', 'inventory_mode', 'prep_time_minutes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="price">Price</FieldLabel>
                        <Input
                            id="price"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.price}
                            aria-invalid={!!errors.price}
                            onChange={(event) => setData('price', event.target.value)}
                        />
                        <InputError message={errors.price} />
                    </Field>
                    <Field>
                        <FieldLabel name="sale_price">Sale price</FieldLabel>
                        <Input
                            id="sale_price"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.sale_price}
                            aria-invalid={!!errors.sale_price}
                            onChange={(event) => setData('sale_price', event.target.value)}
                        />
                        <FieldDescription>Leave empty when not on sale.</FieldDescription>
                        <InputError message={errors.sale_price} />
                    </Field>
                    <Field>
                        <FieldLabel name="sale_ends_at">Sale ends</FieldLabel>
                        <DateTimePicker
                            id="sale_ends_at"
                            value={data.sale_ends_at}
                            invalid={!!errors.sale_ends_at}
                            onChange={(value) => setData('sale_ends_at', value)}
                        />
                        <InputError message={errors.sale_ends_at} />
                    </Field>
                    <Field>
                        <FieldLabel name="stock_qty">In stock</FieldLabel>
                        <Input
                            id="stock_qty"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={0}
                            value={data.stock_qty}
                            aria-invalid={!!errors.stock_qty}
                            onChange={(event) => setData('stock_qty', event.target.value)}
                        />
                        <InputError message={errors.stock_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="min_stock_qty">Low-stock level</FieldLabel>
                        <Input
                            id="min_stock_qty"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={0}
                            value={data.min_stock_qty}
                            aria-invalid={!!errors.min_stock_qty}
                            onChange={(event) => setData('min_stock_qty', event.target.value)}
                        />
                        <InputError message={errors.min_stock_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="inventory_mode">Stock mode</FieldLabel>
                        <SelectDropdown
                            id="inventory_mode"
                            value={data.inventory_mode || null}
                            options={inventoryModeOptions}
                            invalid={!!errors.inventory_mode}
                            onChange={(value) => setData('inventory_mode', value === null ? 'made_to_order' : String(value))}
                        />
                        <InputError message={errors.inventory_mode} />
                    </Field>
                    <Field>
                        <FieldLabel name="prep_time_minutes">Prep time (minutes)</FieldLabel>
                        <Input
                            id="prep_time_minutes"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={0}
                            value={data.prep_time_minutes}
                            aria-invalid={!!errors.prep_time_minutes}
                            onChange={(event) => setData('prep_time_minutes', event.target.value)}
                        />
                        <InputError message={errors.prep_time_minutes} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Layers}
                title="Options"
                description="Variations and flavor choices customers pick from."
                fields={['variation_type', 'variations', 'is_assorted', 'max_flavors', 'flavor', 'flavors']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="variation_type">Variation type</FieldLabel>
                        <SelectDropdown
                            id="variation_type"
                            value={data.variation_type || null}
                            options={variationTypeOptions}
                            invalid={!!errors.variation_type}
                            onChange={(value) => setData('variation_type', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.variation_type} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="variations">Variations</FieldLabel>
                        <RepeaterField
                            name="variations"
                            rows={data.variations}
                            columns={[
                                { name: 'label', label: 'Label', placeholder: 'e.g. 500g / Box of 6', width: '2fr' },
                                { name: 'price_modifier', label: 'Price adjustment (₱)', type: 'number', step: '0.01', width: '10rem' },
                            ]}
                            onChange={(rows) => setData('variations', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add variation"
                        />
                    </Field>
                    <Field>
                        <FieldLabel name="is_assorted">Assorted box</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_assorted" checked={data.is_assorted} onCheckedChange={(checked) => setData('is_assorted', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_assorted ? 'Customers pick flavors' : 'Single flavor'}</span>
                        </div>
                        <InputError message={errors.is_assorted} />
                    </Field>
                    <Field>
                        <FieldLabel name="max_flavors">Max flavors per box</FieldLabel>
                        <Input
                            id="max_flavors"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={1}
                            value={data.max_flavors}
                            aria-invalid={!!errors.max_flavors}
                            onChange={(event) => setData('max_flavors', event.target.value)}
                        />
                        <InputError message={errors.max_flavors} />
                    </Field>
                    <Field>
                        <FieldLabel name="flavor">Default flavor</FieldLabel>
                        <Input
                            id="flavor"
                            type="text"
                            value={data.flavor}
                            aria-invalid={!!errors.flavor}
                            onChange={(event) => setData('flavor', event.target.value)}
                        />
                        <InputError message={errors.flavor} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="flavors">Flavor choices</FieldLabel>
                        <RepeaterField
                            name="flavors"
                            rows={data.flavors}
                            columns={[
                                { name: 'name', label: 'Flavor', placeholder: 'e.g. Ube, Dark chocolate', width: '2fr' },
                                { name: 'price_modifier', label: 'Price adjustment (₱)', type: 'number', step: '0.01', width: '10rem' },
                            ]}
                            onChange={(rows) => setData('flavors', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add flavor"
                        />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Eye}
                title="Visibility"
                description="Badges and where it appears."
                fields={['is_featured', 'is_best_seller', 'is_new_arrival', 'is_seasonal', 'is_limited', 'is_active', 'seo_title', 'seo_description']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="is_featured">Featured</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_featured" checked={data.is_featured} onCheckedChange={(checked) => setData('is_featured', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_featured ? 'On the homepage' : 'Not featured'}</span>
                        </div>
                        <InputError message={errors.is_featured} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_best_seller">Best seller (manual)</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch
                                id="is_best_seller"
                                checked={data.is_best_seller}
                                onCheckedChange={(checked) => setData('is_best_seller', checked)}
                            />
                            <span className="text-sm text-muted-foreground">
                                {data.is_best_seller ? 'Always shown as best seller' : 'Automatic (by sales)'}
                            </span>
                        </div>
                        <InputError message={errors.is_best_seller} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_new_arrival">New arrival (manual)</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch
                                id="is_new_arrival"
                                checked={data.is_new_arrival}
                                onCheckedChange={(checked) => setData('is_new_arrival', checked)}
                            />
                            <span className="text-sm text-muted-foreground">
                                {data.is_new_arrival ? 'Always shown as new' : 'Automatic (first 30 days)'}
                            </span>
                        </div>
                        <InputError message={errors.is_new_arrival} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_seasonal">Seasonal</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_seasonal" checked={data.is_seasonal} onCheckedChange={(checked) => setData('is_seasonal', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_seasonal ? 'Yes' : 'No'}</span>
                        </div>
                        <InputError message={errors.is_seasonal} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_limited">Limited edition</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_limited" checked={data.is_limited} onCheckedChange={(checked) => setData('is_limited', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_limited ? 'Yes' : 'No'}</span>
                        </div>
                        <InputError message={errors.is_limited} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_active">Active</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_active" checked={data.is_active} onCheckedChange={(checked) => setData('is_active', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_active ? 'Sold on the storefront' : 'Hidden'}</span>
                        </div>
                        <InputError message={errors.is_active} />
                    </Field>
                    <Field>
                        <FieldLabel name="seo_title">SEO title</FieldLabel>
                        <Input
                            id="seo_title"
                            type="text"
                            value={data.seo_title}
                            aria-invalid={!!errors.seo_title}
                            onChange={(event) => setData('seo_title', event.target.value)}
                        />
                        <InputError message={errors.seo_title} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="seo_description">SEO description</FieldLabel>
                        <Textarea
                            id="seo_description"
                            rows={3}
                            value={data.seo_description}
                            aria-invalid={!!errors.seo_description}
                            onChange={(event) => setData('seo_description', event.target.value)}
                        />
                        <InputError message={errors.seo_description} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
