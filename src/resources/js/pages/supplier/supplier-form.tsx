import { Building2, MapPin } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface SupplierFormData {
    name: string;
    contact_person: string;
    email: string;
    phone: string;
    payment_terms: string;
    address: string;
    notes: string;
}

export type SupplierFormOptions = Record<string, never>;

interface Props {
    data: SupplierFormData;
    setData: <K extends keyof SupplierFormData>(key: K, value: SupplierFormData[K]) => void;
    errors: Partial<Record<keyof SupplierFormData, string>>;
    options: SupplierFormOptions;
}

/** Presentational: create.tsx / edit.tsx own the state. */
export function SupplierForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={Building2}
                title="Details"
                description="Who you deal with and how you pay them."
                fields={['name', 'contact_person', 'email', 'phone', 'payment_terms']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Company name</FieldLabel>
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
                        <FieldLabel name="contact_person">Contact person</FieldLabel>
                        <Input
                            id="contact_person"
                            type="text"
                            value={data.contact_person}
                            aria-invalid={!!errors.contact_person}
                            onChange={(event) => setData('contact_person', event.target.value)}
                        />
                        <InputError message={errors.contact_person} />
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
                        <FieldLabel name="payment_terms">Payment terms</FieldLabel>
                        <Input
                            id="payment_terms"
                            type="text"
                            value={data.payment_terms}
                            aria-invalid={!!errors.payment_terms}
                            onChange={(event) => setData('payment_terms', event.target.value)}
                        />
                        <FieldDescription>e.g. Net 30, COD, 50% down.</FieldDescription>
                        <InputError message={errors.payment_terms} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={MapPin}
                title="Address & notes"
                description="Where deliveries come from."
                fields={['address', 'notes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="address">Address</FieldLabel>
                        <Textarea
                            id="address"
                            rows={3}
                            value={data.address}
                            aria-invalid={!!errors.address}
                            onChange={(event) => setData('address', event.target.value)}
                        />
                        <InputError message={errors.address} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="notes">Notes</FieldLabel>
                        <Textarea
                            id="notes"
                            rows={3}
                            value={data.notes}
                            aria-invalid={!!errors.notes}
                            onChange={(event) => setData('notes', event.target.value)}
                        />
                        <InputError message={errors.notes} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
