import { DateTimePicker } from '@/components/custom/date-picker';
import { Truck, User, Wallet } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface OrderFormData {
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    delivery_address: string;
    city: string;
    postal_code: string;
    scheduled_time: string;
    notes: string;
    payment_status: string;
    payment_reference: string;
}

export type OrderFormOptions = Record<string, never>;

interface Props {
    data: OrderFormData;
    setData: <K extends keyof OrderFormData>(key: K, value: OrderFormData[K]) => void;
    errors: Partial<Record<keyof OrderFormData, string>>;
    options: OrderFormOptions;
}

const paymentStatusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'awaiting_payment', label: 'Awaiting payment' },
    { value: 'paid', label: 'Paid' },
    { value: 'failed', label: 'Failed' },
    { value: 'refunded', label: 'Refunded' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function OrderForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={User} title="Details" description="" fields={['customer_name', 'customer_email', 'customer_phone']} errors={errors}>
                <div className={fieldGrid}>
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
                icon={Truck}
                title="Delivery"
                description=""
                fields={['delivery_address', 'city', 'postal_code', 'scheduled_time', 'notes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="delivery_address">Delivery address</FieldLabel>
                        <Textarea
                            id="delivery_address"
                            rows={3}
                            value={data.delivery_address}
                            aria-invalid={!!errors.delivery_address}
                            onChange={(event) => setData('delivery_address', event.target.value)}
                        />
                        <InputError message={errors.delivery_address} />
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
                        <FieldLabel name="postal_code">Postal code</FieldLabel>
                        <Input
                            id="postal_code"
                            type="number"
                            step="1"
                            min={0}
                            maxLength={4}
                            value={data.postal_code}
                            aria-invalid={!!errors.postal_code}
                            onChange={(event) => setData('postal_code', event.target.value)}
                        />
                        <InputError message={errors.postal_code} />
                    </Field>
                    <Field>
                        <FieldLabel name="scheduled_time">Scheduled for</FieldLabel>
                        <DateTimePicker
                            id="scheduled_time"
                            value={data.scheduled_time}
                            invalid={!!errors.scheduled_time}
                            onChange={(value) => setData('scheduled_time', value)}
                        />
                        <InputError message={errors.scheduled_time} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="notes">Customer notes</FieldLabel>
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
            <FormSection icon={Wallet} title="Payment" description="" fields={['payment_status', 'payment_reference']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="payment_status">Payment status</FieldLabel>
                        <SelectDropdown
                            id="payment_status"
                            value={data.payment_status || null}
                            options={paymentStatusOptions}
                            invalid={!!errors.payment_status}
                            onChange={(value) => setData('payment_status', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.payment_status} />
                    </Field>
                    <Field>
                        <FieldLabel name="payment_reference">Payment reference</FieldLabel>
                        <Input
                            id="payment_reference"
                            type="text"
                            value={data.payment_reference}
                            aria-invalid={!!errors.payment_reference}
                            onChange={(event) => setData('payment_reference', event.target.value)}
                        />
                        <FieldDescription>GCash / bank reference number, if paid manually.</FieldDescription>
                        <InputError message={errors.payment_reference} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
