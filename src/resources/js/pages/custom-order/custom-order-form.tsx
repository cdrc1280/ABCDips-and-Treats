import { Cake, Receipt, User } from 'lucide-react';

import { DatePicker } from '@/components/custom/date-picker';
import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface CustomOrderFormData {
    reference_number: string;
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    event_date: string;
    servings_count: string;
    tiers_count: string;
    flavor_preference: string;
    frosting_type: string;
    theme_description: string;
    budget_range_min: string;
    budget_range_max: string;
    quoted_price: string;
    status: string;
    staff_notes: string;
}

export type CustomOrderFormOptions = Record<string, never>;

interface Props {
    data: CustomOrderFormData;
    setData: <K extends keyof CustomOrderFormData>(key: K, value: CustomOrderFormData[K]) => void;
    errors: Partial<Record<keyof CustomOrderFormData, string>>;
    options: CustomOrderFormOptions;
}

const statusOptions = [
    { value: 'inquiry', label: 'Inquiry' },
    { value: 'quoted', label: 'Quoted' },
    { value: 'deposit_paid', label: 'Deposit paid' },
    { value: 'in_production', label: 'In production' },
    { value: 'ready', label: 'Ready' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function CustomOrderForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={User}
                title="Details"
                description=""
                fields={['reference_number', 'customer_name', 'customer_email', 'customer_phone']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="reference_number">Reference</FieldLabel>
                        <Input
                            id="reference_number"
                            type="text"
                            value={data.reference_number}
                            aria-invalid={!!errors.reference_number}
                            onChange={(event) => setData('reference_number', event.target.value)}
                        />
                        <FieldDescription>Leave empty to generate.</FieldDescription>
                        <InputError message={errors.reference_number} />
                    </Field>
                    <Field>
                        <FieldLabel name="customer_name">Customer name</FieldLabel>
                        <Input
                            id="customer_name"
                            type="text"
                            value={data.customer_name}
                            aria-invalid={!!errors.customer_name}
                            onChange={(event) => setData('customer_name', event.target.value)}
                        />
                        <InputError message={errors.customer_name} />
                    </Field>
                    <Field>
                        <FieldLabel name="customer_email">Email</FieldLabel>
                        <Input
                            id="customer_email"
                            type="email"
                            value={data.customer_email}
                            aria-invalid={!!errors.customer_email}
                            onChange={(event) => setData('customer_email', event.target.value)}
                        />
                        <InputError message={errors.customer_email} />
                    </Field>
                    <Field>
                        <FieldLabel name="customer_phone">Phone</FieldLabel>
                        <Input
                            id="customer_phone"
                            type="tel"
                            value={data.customer_phone}
                            aria-invalid={!!errors.customer_phone}
                            onChange={(event) => setData('customer_phone', event.target.value)}
                        />
                        <InputError message={errors.customer_phone} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Cake}
                title="Cake"
                description=""
                fields={['event_date', 'servings_count', 'tiers_count', 'flavor_preference', 'frosting_type', 'theme_description']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="event_date">Event date</FieldLabel>
                        <DatePicker
                            id="event_date"
                            value={data.event_date || null}
                            invalid={!!errors.event_date}
                            onChange={(value) => setData('event_date', value ?? '')}
                        />
                        <InputError message={errors.event_date} />
                    </Field>
                    <Field>
                        <FieldLabel name="servings_count">Servings</FieldLabel>
                        <Input
                            id="servings_count"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={1}
                            value={data.servings_count}
                            aria-invalid={!!errors.servings_count}
                            onChange={(event) => setData('servings_count', event.target.value)}
                        />
                        <InputError message={errors.servings_count} />
                    </Field>
                    <Field>
                        <FieldLabel name="tiers_count">Tiers</FieldLabel>
                        <Input
                            id="tiers_count"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={1}
                            value={data.tiers_count}
                            aria-invalid={!!errors.tiers_count}
                            onChange={(event) => setData('tiers_count', event.target.value)}
                        />
                        <InputError message={errors.tiers_count} />
                    </Field>
                    <Field>
                        <FieldLabel name="flavor_preference">Flavor</FieldLabel>
                        <Input
                            id="flavor_preference"
                            type="text"
                            value={data.flavor_preference}
                            aria-invalid={!!errors.flavor_preference}
                            onChange={(event) => setData('flavor_preference', event.target.value)}
                        />
                        <InputError message={errors.flavor_preference} />
                    </Field>
                    <Field>
                        <FieldLabel name="frosting_type">Frosting</FieldLabel>
                        <Input
                            id="frosting_type"
                            type="text"
                            value={data.frosting_type}
                            aria-invalid={!!errors.frosting_type}
                            onChange={(event) => setData('frosting_type', event.target.value)}
                        />
                        <InputError message={errors.frosting_type} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="theme_description">Theme & design</FieldLabel>
                        <Textarea
                            id="theme_description"
                            rows={3}
                            value={data.theme_description}
                            aria-invalid={!!errors.theme_description}
                            onChange={(event) => setData('theme_description', event.target.value)}
                        />
                        <InputError message={errors.theme_description} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Receipt}
                title="Quote"
                description=""
                fields={['budget_range_min', 'budget_range_max', 'quoted_price', 'status', 'staff_notes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="budget_range_min">Budget from</FieldLabel>
                        <Input
                            id="budget_range_min"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.budget_range_min}
                            aria-invalid={!!errors.budget_range_min}
                            onChange={(event) => setData('budget_range_min', event.target.value)}
                        />
                        <InputError message={errors.budget_range_min} />
                    </Field>
                    <Field>
                        <FieldLabel name="budget_range_max">Budget to</FieldLabel>
                        <Input
                            id="budget_range_max"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.budget_range_max}
                            aria-invalid={!!errors.budget_range_max}
                            onChange={(event) => setData('budget_range_max', event.target.value)}
                        />
                        <InputError message={errors.budget_range_max} />
                    </Field>
                    <Field>
                        <FieldLabel name="quoted_price">Quoted price</FieldLabel>
                        <Input
                            id="quoted_price"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.quoted_price}
                            aria-invalid={!!errors.quoted_price}
                            onChange={(event) => setData('quoted_price', event.target.value)}
                        />
                        <InputError message={errors.quoted_price} />
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
                        <FieldLabel name="staff_notes">Staff notes</FieldLabel>
                        <Textarea
                            id="staff_notes"
                            rows={3}
                            value={data.staff_notes}
                            aria-invalid={!!errors.staff_notes}
                            onChange={(event) => setData('staff_notes', event.target.value)}
                        />
                        <InputError message={errors.staff_notes} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
