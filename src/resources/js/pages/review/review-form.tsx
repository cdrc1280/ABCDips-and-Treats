import { Eye, Star } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface ReviewFormData {
    product_id: string;
    rating: string;
    reviewer_name: string;
    reviewer_email: string;
    title: string;
    comment: string;
    is_approved: boolean;
    is_featured: boolean;
    is_verified_buyer: boolean;
    is_anonymous: boolean;
}

export type ReviewFormOptions = { product_options?: Option<string>[] };

interface Props {
    data: ReviewFormData;
    setData: <K extends keyof ReviewFormData>(key: K, value: ReviewFormData[K]) => void;
    errors: Partial<Record<keyof ReviewFormData, string>>;
    options: ReviewFormOptions;
}

const ratingOptions = [
    { value: '5', label: '★★★★★ 5' },
    { value: '4', label: '★★★★ 4' },
    { value: '3', label: '★★★ 3' },
    { value: '2', label: '★★ 2' },
    { value: '1', label: '★ 1' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function ReviewForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={Star}
                title="Details"
                description=""
                fields={['product_id', 'rating', 'reviewer_name', 'reviewer_email', 'title', 'comment']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="product_id">Product</FieldLabel>
                        <SelectDropdown<string>
                            id="product_id"
                            clearable
                            value={data.product_id ? String(data.product_id) : null}
                            options={options.product_options ?? []}
                            invalid={!!errors.product_id}
                            onChange={(value) => setData('product_id', value === null ? '' : String(value))}
                        />
                        <FieldDescription>Leave empty for a store / service review.</FieldDescription>
                        <InputError message={errors.product_id} />
                    </Field>
                    <Field>
                        <FieldLabel name="rating">Rating</FieldLabel>
                        <SelectDropdown
                            id="rating"
                            value={data.rating || null}
                            options={ratingOptions}
                            invalid={!!errors.rating}
                            onChange={(value) => setData('rating', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.rating} />
                    </Field>
                    <Field>
                        <FieldLabel name="reviewer_name">Reviewer name</FieldLabel>
                        <Input
                            id="reviewer_name"
                            type="text"
                            value={data.reviewer_name}
                            aria-invalid={!!errors.reviewer_name}
                            onChange={(event) => setData('reviewer_name', event.target.value)}
                        />
                        <InputError message={errors.reviewer_name} />
                    </Field>
                    <Field>
                        <FieldLabel name="reviewer_email">Reviewer email</FieldLabel>
                        <Input
                            id="reviewer_email"
                            type="email"
                            value={data.reviewer_email}
                            aria-invalid={!!errors.reviewer_email}
                            onChange={(event) => setData('reviewer_email', event.target.value)}
                        />
                        <InputError message={errors.reviewer_email} />
                    </Field>
                    <Field>
                        <FieldLabel name="title">Headline</FieldLabel>
                        <Input
                            id="title"
                            type="text"
                            value={data.title}
                            aria-invalid={!!errors.title}
                            onChange={(event) => setData('title', event.target.value)}
                        />
                        <InputError message={errors.title} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="comment">Review</FieldLabel>
                        <Textarea
                            id="comment"
                            rows={3}
                            value={data.comment}
                            aria-invalid={!!errors.comment}
                            onChange={(event) => setData('comment', event.target.value)}
                        />
                        <InputError message={errors.comment} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Eye}
                title="Visibility"
                description=""
                fields={['is_approved', 'is_featured', 'is_verified_buyer', 'is_anonymous']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="is_approved">Approved</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_approved" checked={data.is_approved} onCheckedChange={(checked) => setData('is_approved', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_approved ? 'Shown publicly' : 'Hidden'}</span>
                        </div>
                        <InputError message={errors.is_approved} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_featured">Featured</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_featured" checked={data.is_featured} onCheckedChange={(checked) => setData('is_featured', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_featured ? 'Highlighted on the homepage' : 'Not featured'}</span>
                        </div>
                        <InputError message={errors.is_featured} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_verified_buyer">Verified buyer</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch
                                id="is_verified_buyer"
                                checked={data.is_verified_buyer}
                                onCheckedChange={(checked) => setData('is_verified_buyer', checked)}
                            />
                            <span className="text-sm text-muted-foreground">{data.is_verified_buyer ? 'Bought this product' : 'Unverified'}</span>
                        </div>
                        <InputError message={errors.is_verified_buyer} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_anonymous">Anonymous</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_anonymous" checked={data.is_anonymous} onCheckedChange={(checked) => setData('is_anonymous', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_anonymous ? 'Name hidden publicly' : 'Name shown'}</span>
                        </div>
                        <InputError message={errors.is_anonymous} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
