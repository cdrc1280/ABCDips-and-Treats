import { MessagesSquare } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface ChatEscalationFormData {
    guest_name: string;
    guest_email: string;
    status: string;
    admin_notes: string;
}

export type ChatEscalationFormOptions = Record<string, never>;

interface Props {
    data: ChatEscalationFormData;
    setData: <K extends keyof ChatEscalationFormData>(key: K, value: ChatEscalationFormData[K]) => void;
    errors: Partial<Record<keyof ChatEscalationFormData, string>>;
    options: ChatEscalationFormOptions;
}

const statusOptions = [
    { value: 'open', label: 'Open' },
    { value: 'in_progress', label: 'In progress' },
    { value: 'resolved', label: 'Resolved' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function ChatEscalationForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={MessagesSquare}
                title="Details"
                description=""
                fields={['guest_name', 'guest_email', 'status', 'admin_notes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="guest_name">Customer name</FieldLabel>
                        <Input
                            id="guest_name"
                            type="text"
                            value={data.guest_name}
                            aria-invalid={!!errors.guest_name}
                            onChange={(event) => setData('guest_name', event.target.value)}
                        />
                        <InputError message={errors.guest_name} />
                    </Field>
                    <Field>
                        <FieldLabel name="guest_email">Customer email</FieldLabel>
                        <Input
                            id="guest_email"
                            type="email"
                            value={data.guest_email}
                            aria-invalid={!!errors.guest_email}
                            onChange={(event) => setData('guest_email', event.target.value)}
                        />
                        <InputError message={errors.guest_email} />
                    </Field>
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
