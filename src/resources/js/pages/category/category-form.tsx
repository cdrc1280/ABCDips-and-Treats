import { FileText, Image as ImageIcon, Settings2, X } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

export interface CategoryFormData {
    name: string;
    slug: string;
    description: string;
    sort_order: string;
    is_active: boolean;
    image: File | null;
    remove_image: boolean;
}

interface Props {
    data: CategoryFormData;
    setData: <K extends keyof CategoryFormData>(key: K, value: CategoryFormData[K]) => void;
    errors: Partial<Record<keyof CategoryFormData, string>>;
    isEdit?: boolean;
    currentImageUrl?: string | null;
}

const slugify = (value: string) =>
    value
        .toLowerCase()
        .normalize('NFKD')
        .replace(/[^\w\s-]/g, '')
        .trim()
        .replace(/[\s_]+/g, '-')
        .replace(/-+/g, '-');

/** Presentational: create.tsx / edit.tsx own the state. */
export function CategoryForm({ data, setData, errors, isEdit = false, currentImageUrl }: Props) {
    const showCurrent = currentImageUrl && !data.remove_image && !data.image;

    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={FileText}
                title="Details"
                description="What customers see on the storefront."
                fields={['name', 'slug', 'description']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Name</FieldLabel>
                        <Input
                            id="name"
                            value={data.name}
                            aria-invalid={!!errors.name}
                            onChange={(event) => {
                                const name = event.target.value;
                                if (!isEdit && data.slug === slugify(data.name)) setData('slug', slugify(name));
                                setData('name', name);
                            }}
                            placeholder="e.g. Banana Bread Loaves"
                        />
                        <InputError message={errors.name} />
                    </Field>
                    <Field>
                        <FieldLabel name="slug">URL slug</FieldLabel>
                        <Input
                            id="slug"
                            value={data.slug}
                            aria-invalid={!!errors.slug}
                            onChange={(event) => setData('slug', slugify(event.target.value))}
                            placeholder="banana-bread-loaves"
                        />
                        <FieldDescription>Used in the shop link: /category/{data.slug || 'your-slug'}</FieldDescription>
                        <InputError message={errors.slug} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="description">Description</FieldLabel>
                        <Textarea
                            id="description"
                            rows={3}
                            value={data.description}
                            aria-invalid={!!errors.description}
                            onChange={(event) => setData('description', event.target.value)}
                        />
                        <InputError message={errors.description} />
                    </Field>
                </div>
            </FormSection>

            <FormSection
                icon={ImageIcon}
                title="Image"
                description="Shown on category tiles. JPG, PNG or WebP up to 4 MB."
                fields={['image']}
                errors={errors}
            >
                <div className="flex flex-wrap items-center gap-4">
                    {showCurrent && (
                        <div className="relative">
                            <img src={currentImageUrl} alt="" className="size-24 rounded-lg border object-cover" />
                            <Button
                                type="button"
                                size="icon"
                                variant="outline"
                                className="absolute -top-2 -right-2 size-6 rounded-full"
                                aria-label="Remove image"
                                onClick={() => setData('remove_image', true)}
                            >
                                <X className="size-3" />
                            </Button>
                        </div>
                    )}
                    <Field className="min-w-60 flex-1">
                        <FieldLabel name="image">{showCurrent ? 'Replace image' : 'Upload image'}</FieldLabel>
                        <Input
                            id="image"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            aria-invalid={!!errors.image}
                            onChange={(event) => {
                                setData('image', event.target.files?.[0] ?? null);
                                setData('remove_image', false);
                            }}
                        />
                        <InputError message={errors.image} />
                    </Field>
                </div>
            </FormSection>

            <FormSection icon={Settings2} title="Display" description="Ordering and visibility." fields={['sort_order', 'is_active']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="sort_order">Sort order</FieldLabel>
                        <Input
                            id="sort_order"
                            type="number"
                            min={0}
                            value={data.sort_order}
                            aria-invalid={!!errors.sort_order}
                            onChange={(event) => setData('sort_order', event.target.value)}
                            placeholder="Auto"
                        />
                        <FieldDescription>Lower numbers appear first. Leave empty to place it last.</FieldDescription>
                        <InputError message={errors.sort_order} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_active">Visible on storefront</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_active" checked={data.is_active} onCheckedChange={(checked) => setData('is_active', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_active ? 'Shown in the shop' : 'Hidden from customers'}</span>
                        </div>
                        <InputError message={errors.is_active} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
