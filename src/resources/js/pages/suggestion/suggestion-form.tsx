import { ClipboardCheck, Lightbulb } from 'lucide-react';

import { Field, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface SuggestionFormData {
    name: string;
    email: string;
    category: string;
    subject: string;
    message: string;
    status: string;
    admin_notes: string;
}

export type SuggestionFormOptions = Record<string, never>;

interface Props {
    data: SuggestionFormData;
    setData: <K extends keyof SuggestionFormData>(key: K, value: SuggestionFormData[K]) => void;
    errors: Partial<Record<keyof SuggestionFormData, string>>;
    options: SuggestionFormOptions;
}

const categoryOptions = [
    { value: 'product_idea', label: 'Product idea' },
    { value: 'service_feedback', label: 'Service feedback' },
    { value: 'feature_request', label: 'Feature request' },
    { value: 'other', label: 'Other' },
];
const statusOptions = [
    { value: 'new', label: 'New' },
    { value: 'reviewing', label: 'Reviewing' },
    { value: 'implemented', label: 'Implemented' },
    { value: 'declined', label: 'Declined' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function SuggestionForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={Lightbulb} title="Details" description="" fields={['name', 'email', 'category', 'subject', 'message']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Name</FieldLabel>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            aria-invalid={!!errors.name}
                            onChange={(event) => setData('name', event.target.value)}
                        />
                        <InputError message={errors.name} />
                    </Field>
                    <Field>
                        <FieldLabel name="email">Email</FieldLabel>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            aria-invalid={!!errors.email}
                            onChange={(event) => setData('email', event.target.value)}
                        />
                        <InputError message={errors.email} />
                    </Field>
                    <Field>
                        <FieldLabel name="category">Category</FieldLabel>
                        <SelectDropdown
                            id="category"
                            value={data.category || null}
                            options={categoryOptions}
                            invalid={!!errors.category}
                            onChange={(value) => setData('category', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.category} />
                    </Field>
                    <Field>
                        <FieldLabel name="subject">Subject</FieldLabel>
                        <Input
                            id="subject"
                            type="text"
                            value={data.subject}
                            aria-invalid={!!errors.subject}
                            onChange={(event) => setData('subject', event.target.value)}
                        />
                        <InputError message={errors.subject} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="message">Message</FieldLabel>
                        <Textarea
                            id="message"
                            rows={3}
                            value={data.message}
                            aria-invalid={!!errors.message}
                            onChange={(event) => setData('message', event.target.value)}
                        />
                        <InputError message={errors.message} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={ClipboardCheck} title="Follow-up" description="" fields={['status', 'admin_notes']} errors={errors}>
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
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="admin_notes">Internal notes</FieldLabel>
                        <Textarea
                            id="admin_notes"
                            rows={3}
                            value={data.admin_notes}
                            aria-invalid={!!errors.admin_notes}
                            onChange={(event) => setData('admin_notes', event.target.value)}
                        />
                        <InputError message={errors.admin_notes} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
