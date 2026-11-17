import { MapPin, User } from 'lucide-react';

import { Field, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

export interface CustomerFormData {
    name: string;
    email: string;
    phone: string;
    street_address: string;
    barangay: string;
    city: string;
    province: string;
    region: string;
}

export type CustomerFormOptions = Record<string, never>;

interface Props {
    data: CustomerFormData;
    setData: <K extends keyof CustomerFormData>(key: K, value: CustomerFormData[K]) => void;
    errors: Partial<Record<keyof CustomerFormData, string>>;
    options: CustomerFormOptions;
}

/** Presentational: create.tsx / edit.tsx own the state. */
export function CustomerForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={User} title="Details" description="" fields={['name', 'email', 'phone']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Full name</FieldLabel>
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
                </div>
            </FormSection>
            <FormSection
                icon={MapPin}
                title="Address"
                description=""
                fields={['street_address', 'barangay', 'city', 'province', 'region']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="street_address">Street address</FieldLabel>
                        <Input
                            id="street_address"
                            type="text"
                            value={data.street_address}
                            aria-invalid={!!errors.street_address}
                            onChange={(event) => setData('street_address', event.target.value)}
                        />
                        <InputError message={errors.street_address} />
                    </Field>
                    <Field>
                        <FieldLabel name="barangay">Barangay</FieldLabel>
                        <Input
                            id="barangay"
                            type="text"
                            value={data.barangay}
                            aria-invalid={!!errors.barangay}
                            onChange={(event) => setData('barangay', event.target.value)}
                        />
                        <InputError message={errors.barangay} />
                    </Field>
                    <Field>
                        <FieldLabel name="city">City</FieldLabel>
                        <Input
                            id="city"
                            type="text"
                            value={data.city}
                            aria-invalid={!!errors.city}
                            onChange={(event) => setData('city', event.target.value)}
                        />
                        <InputError message={errors.city} />
                    </Field>
                    <Field>
                        <FieldLabel name="province">Province</FieldLabel>
                        <Input
                            id="province"
                            type="text"
                            value={data.province}
                            aria-invalid={!!errors.province}
                            onChange={(event) => setData('province', event.target.value)}
                        />
                        <InputError message={errors.province} />
                    </Field>
                    <Field>
                        <FieldLabel name="region">Region</FieldLabel>
                        <Input
                            id="region"
                            type="text"
                            value={data.region}
                            aria-invalid={!!errors.region}
                            onChange={(event) => setData('region', event.target.value)}
                        />
                        <InputError message={errors.region} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
