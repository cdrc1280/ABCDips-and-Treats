import { Head, useForm } from '@inertiajs/react';
import { Banknote, Home, Image as ImageIcon, KeyRound, Loader2, Sparkles, Store, Truck, Users, type LucideIcon } from 'lucide-react';
import type { FormEvent } from 'react';

import { AppFooter } from '@/components/custom/app-footer';
import { Field, FieldDescription, InputError, Required, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { UnsavedChangesGuard } from '@/components/custom/unsaved-changes-guard';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { usePermissions } from '@/hooks/use-permissions';
import { SettingPermission } from '@/lib/permissions';
import { dashboard } from '@/routes/admin';
import { update } from '@/routes/admin/settings';
import { PageHeader } from '@/components/custom/page-header';

interface SettingField {
    key: string;
    label: string;
    type: 'text' | 'number' | 'tel' | 'email' | 'textarea' | 'bool' | 'image' | 'select' | 'secret' | 'json_list';
    required: boolean;
    /** Number field that may be negative (coordinates). */
    signed?: boolean;
    options?: Record<string, string>;
    columns?: Record<string, string>;
}

type Value = string | boolean | File | null | Record<string, string>[];

interface Props {
    sections: { title: string; fields: SettingField[] }[];
    values: Record<string, Value>;
    secrets_set: Record<string, boolean>;
    image_urls: Record<string, string | null>;
}

const sectionIcons: Record<string, LucideIcon> = {
    'Store information': Store,
    'Homepage hero': Home,
    'Homepage spotlight': Sparkles,
    'About page': Users,
    'Lalamove delivery': Truck,
    'PayMongo (GCash, Maya, QR Ph)': KeyRound,
    'BDO bank transfer': Banknote,
};

const title = 'Store settings';

/** Rendered from App\Domains\Setting\Support\StoreSettingsSchema — no field is hard-coded here. */
export default function StoreSettings({ sections, values, secrets_set, image_urls }: Props) {
    const { can } = usePermissions();
    const editable = can(SettingPermission.SettingsEdit);
    const form = useForm<{ values: Record<string, Value>; remove: Record<string, boolean> }>({ values, remove: {} });
    const errors = form.errors as Record<string, string | undefined>;

    const set = (key: string, value: Value) => form.setData('values', { ...form.data.values, [key]: value });

    const submit = (event?: FormEvent) => {
        event?.preventDefault();
        form.transform((data) => ({ ...data, _method: 'put' }));
        form.post(update().url, { forceFormData: true, preserveScroll: true, onSuccess: () => form.setDefaults() });
    };

    const renderField = (field: SettingField) => {
        const id = `setting-${field.key}`;
        const error = errors[`values.${field.key}`];
        const value = form.data.values[field.key];
        const label = (
            <Label htmlFor={id} className="gap-1">
                {field.label}
                {field.required && <Required />}
            </Label>
        );

        switch (field.type) {
            case 'bool':
                return (
                    <Field key={field.key}>
                        {label}
                        <div className="flex h-9 items-center">
                            <Switch id={id} checked={Boolean(value)} disabled={!editable} onCheckedChange={(checked) => set(field.key, checked)} />
                        </div>
                    </Field>
                );
            case 'textarea':
                return (
                    <Field key={field.key} className="sm:col-span-2 2xl:col-span-3">
                        {label}
                        <Textarea
                            id={id}
                            rows={3}
                            value={String(value ?? '')}
                            disabled={!editable}
                            aria-invalid={!!error}
                            onChange={(event) => set(field.key, event.target.value)}
                        />
                        <InputError message={error} />
                    </Field>
                );
            case 'select':
                return (
                    <Field key={field.key}>
                        {label}
                        <SelectDropdown
                            id={id}
                            value={String(value ?? '') || null}
                            disabled={!editable}
                            options={Object.entries(field.options ?? {}).map(([v, l]) => ({ value: v, label: l }))}
                            onChange={(v) => set(field.key, v === null ? '' : String(v))}
                        />
                        <InputError message={error} />
                    </Field>
                );
            case 'secret':
                return (
                    <Field key={field.key}>
                        {label}
                        <Input
                            id={id}
                            type="password"
                            autoComplete="new-password"
                            value={String(value ?? '')}
                            disabled={!editable}
                            placeholder={secrets_set[field.key] ? '•••••••• saved — type to replace' : 'Not set'}
                            onChange={(event) => set(field.key, event.target.value)}
                        />
                        <FieldDescription>Stored securely and never shown again. Leave empty to keep the current value.</FieldDescription>
                        <InputError message={error} />
                    </Field>
                );
            case 'image': {
                const current = image_urls[field.key];
                const removed = form.data.remove[field.key];
                return (
                    <Field key={field.key} className="sm:col-span-2 2xl:col-span-3">
                        {label}
                        <div className="flex flex-wrap items-center gap-3">
                            {current && !removed && !(value instanceof File) && (
                                <>
                                    <img src={current} alt="" className="h-16 w-24 rounded-lg border object-cover" />
                                    {editable && (
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={() => form.setData('remove', { ...form.data.remove, [field.key]: true })}
                                        >
                                            <ImageIcon /> Remove
                                        </Button>
                                    )}
                                </>
                            )}
                            {editable && (
                                <Input
                                    id={id}
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    className="max-w-xs"
                                    onChange={(event) => {
                                        set(field.key, event.target.files?.[0] ?? null);
                                        form.setData('remove', { ...form.data.remove, [field.key]: false });
                                    }}
                                />
                            )}
                        </div>
                        <InputError message={error} />
                    </Field>
                );
            }
            case 'json_list':
                return (
                    <Field key={field.key} className="sm:col-span-2 2xl:col-span-3">
                        {label}
                        <RepeaterField
                            name={`values.${field.key}`}
                            rows={(value as Record<string, string>[]) ?? []}
                            columns={Object.entries(field.columns ?? {}).map(([name, columnLabel]) => ({
                                name,
                                label: columnLabel,
                                width: name === 'desc' ? '3fr' : name === 'title' ? '2fr' : '6rem',
                            }))}
                            onChange={(rows) => set(field.key, rows)}
                            errors={errors}
                            addLabel="Add item"
                        />
                    </Field>
                );
            default:
                return (
                    <Field key={field.key}>
                        {label}
                        <Input
                            id={id}
                            type={field.type === 'email' || field.type === 'number' || field.type === 'tel' ? field.type : 'text'}
                            step={field.type === 'number' ? 'any' : undefined}
                            min={field.type === 'number' && !field.signed ? 0 : undefined}
                            value={String(value ?? '')}
                            disabled={!editable}
                            aria-invalid={!!error}
                            onChange={(event) => set(field.key, event.target.value)}
                        />
                        <InputError message={error} />
                    </Field>
                );
        }
    };

    return (
        <UnsavedChangesGuard dirty={form.isDirty && !form.processing && !form.wasSuccessful}>
            <Head title={title} />
            <form onSubmit={submit} className="grid gap-4">
                <PageHeader title={title}>Store details, storefront content, and the keys for payments and delivery.</PageHeader>
                <Card className="gap-0 py-0">
                    {sections.map((section, index) => (
                        <FormSection
                            key={section.title}
                            icon={sectionIcons[section.title] ?? Store}
                            title={section.title}
                            defaultOpen={index === 0}
                            fields={section.fields.map((field) => `values.${field.key}`)}
                            errors={errors}
                        >
                            <div className={fieldGrid}>{section.fields.map(renderField)}</div>
                        </FormSection>
                    ))}
                </Card>
                {editable && (
                    <AppFooter>
                        <Button type="button" variant="outline" size="sm" disabled={!form.isDirty} onClick={() => form.reset()}>
                            Discard changes
                        </Button>
                        <Button type="button" size="sm" disabled={form.processing || !form.isDirty} onClick={() => submit()}>
                            {form.processing && <Loader2 className="animate-spin" />} Save settings
                        </Button>
                    </AppFooter>
                )}
            </form>
        </UnsavedChangesGuard>
    );
}

StoreSettings.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'System' }, { title }] };
