import { DateTimePicker } from '@/components/custom/date-picker';
import { FileText, Newspaper, Send } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface BlogPostFormData {
    title: string;
    slug: string;
    category: string;
    excerpt: string;
    body: string;
    cover_image: File | null;
    status: string;
    published_at: string;
    seo_title: string;
    seo_description: string;
    remove_cover_image: boolean;
}

export type BlogPostFormOptions = Record<string, never>;

interface Props {
    data: BlogPostFormData;
    setData: <K extends keyof BlogPostFormData>(key: K, value: BlogPostFormData[K]) => void;
    errors: Partial<Record<keyof BlogPostFormData, string>>;
    options: BlogPostFormOptions;
    currentUrls?: { cover_image?: string | null };
}

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'published', label: 'Published' },
    { value: 'archived', label: 'Archived' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function BlogPostForm({ data, setData, errors, options, currentUrls }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={Newspaper} title="Details" description="" fields={['title', 'slug', 'category', 'excerpt']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="title">Title</FieldLabel>
                        <Input
                            id="title"
                            type="text"
                            value={data.title}
                            aria-invalid={!!errors.title}
                            onChange={(event) => setData('title', event.target.value)}
                        />
                        <InputError message={errors.title} />
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
                        <FieldDescription>Leave empty to generate from the title.</FieldDescription>
                        <InputError message={errors.slug} />
                    </Field>
                    <Field>
                        <FieldLabel name="category">Category</FieldLabel>
                        <Input
                            id="category"
                            type="text"
                            value={data.category}
                            aria-invalid={!!errors.category}
                            onChange={(event) => setData('category', event.target.value)}
                        />
                        <InputError message={errors.category} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="excerpt">Excerpt</FieldLabel>
                        <Textarea
                            id="excerpt"
                            rows={3}
                            value={data.excerpt}
                            aria-invalid={!!errors.excerpt}
                            onChange={(event) => setData('excerpt', event.target.value)}
                        />
                        <InputError message={errors.excerpt} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={FileText} title="Content" description="" fields={['body', 'cover_image']} errors={errors}>
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="body">Body</FieldLabel>
                        <Textarea
                            id="body"
                            rows={10}
                            value={data.body}
                            aria-invalid={!!errors.body}
                            onChange={(event) => setData('body', event.target.value)}
                        />
                        <FieldDescription>Markdown is supported.</FieldDescription>
                        <InputError message={errors.body} />
                    </Field>
                    <Field>
                        <FieldLabel name="cover_image">Cover image</FieldLabel>
                        {currentUrls?.cover_image && !data.remove_cover_image && !data.cover_image && (
                            <div className="flex items-center gap-3">
                                <img src={currentUrls.cover_image} alt="" className="size-16 rounded-lg border object-cover" />
                                <Button type="button" variant="outline" size="sm" onClick={() => setData('remove_cover_image', true)}>
                                    Remove
                                </Button>
                            </div>
                        )}
                        <Input
                            id="cover_image"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            aria-invalid={!!errors.cover_image}
                            onChange={(event) => {
                                setData('cover_image', event.target.files?.[0] ?? null);
                                setData('remove_cover_image', false);
                            }}
                        />
                        <InputError message={errors.cover_image} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Send}
                title="Publishing"
                description=""
                fields={['status', 'published_at', 'seo_title', 'seo_description']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="status">Status</FieldLabel>
                        <SelectDropdown
                            id="status"
                            value={data.status || null}
                            options={statusOptions}
                            invalid={!!errors.status}
                            onChange={(value) => setData('status', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.status} />
                    </Field>
                    <Field>
                        <FieldLabel name="published_at">Publish date</FieldLabel>
                        <DateTimePicker
                            id="published_at"
                            value={data.published_at}
                            invalid={!!errors.published_at}
                            onChange={(value) => setData('published_at', value)}
                        />
                        <FieldDescription>Set automatically the first time it is published.</FieldDescription>
                        <InputError message={errors.published_at} />
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
