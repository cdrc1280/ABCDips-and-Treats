import { Mail, MessageSquareReply } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface ContactMessageFormData {
    name: string;
    email: string;
    phone: string;
    subject: string;
    message: string;
    status: string;
    admin_notes: string;
}

export type ContactMessageFormOptions = Record<string, never>;

interface Props {
    data: ContactMessageFormData;
    setData: <K extends keyof ContactMessageFormData>(key: K, value: ContactMessageFormData[K]) => void;
    errors: Partial<Record<keyof ContactMessageFormData, string>>;
    options: ContactMessageFormOptions;
}

const statusOptions = [
    { value: 'unread', label: 'Unread' },
    { value: 'read', label: 'Read' },
    { value: 'replied', label: 'Replied' },
    { value: 'archived', label: 'Archived' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function ContactMessageForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={Mail} title="Details" description="" fields={['name', 'email', 'phone', 'subject', 'message']} errors={errors}>
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
                        <FieldLabel name="phone">Phone</FieldLabel>
                        <Input
                            id="phone"
                            type="tel"
                            value={data.phone}
                            aria-invalid={!!errors.phone}
                            onChange={(event) => setData('phone', event.target.value)}
                        />
                        <InputError message={errors.phone} />
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
            <FormSection icon={MessageSquareReply} title="Follow-up" description="" fields={['status', 'admin_notes']} errors={errors}>
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
                        <FieldDescription>Only visible to staff.</FieldDescription>
                        <InputError message={errors.admin_notes} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
