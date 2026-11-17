import { MapPin, Route } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export interface DeliveryPoolFormData {
    pool_code: string;
    zone_name: string;
    region: string;
    province: string;
    city: string;
    barangay: string;
    total_delivery_fee: string;
    status: string;
    allocations: Record<string, string>[];
    notes: string;
}

export type DeliveryPoolFormOptions = Record<string, never>;

interface Props {
    data: DeliveryPoolFormData;
    setData: <K extends keyof DeliveryPoolFormData>(key: K, value: DeliveryPoolFormData[K]) => void;
    errors: Partial<Record<keyof DeliveryPoolFormData, string>>;
    options: DeliveryPoolFormOptions;
}

const statusOptions = [
    { value: 'open', label: 'Open' },
    { value: 'building', label: 'Building' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function DeliveryPoolForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={MapPin}
                title="Details"
                description=""
                fields={['pool_code', 'zone_name', 'region', 'province', 'city', 'barangay']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="pool_code">Pool code</FieldLabel>
                        <Input
                            id="pool_code"
                            type="text"
                            value={data.pool_code}
                            aria-invalid={!!errors.pool_code}
                            onChange={(event) => setData('pool_code', event.target.value)}
                        />
                        <FieldDescription>Leave empty to generate.</FieldDescription>
                        <InputError message={errors.pool_code} />
                    </Field>
                    <Field>
                        <FieldLabel name="zone_name">Zone name</FieldLabel>
                        <Input
                            id="zone_name"
                            type="text"
                            value={data.zone_name}
                            aria-invalid={!!errors.zone_name}
                            onChange={(event) => setData('zone_name', event.target.value)}
                            placeholder="e.g. Imus – Anabu"
                        />
                        <InputError message={errors.zone_name} />
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
                </div>
            </FormSection>
            <FormSection icon={Route} title="Orders" description="" fields={['total_delivery_fee', 'status', 'allocations', 'notes']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="total_delivery_fee">Total delivery fee</FieldLabel>
                        <Input
                            id="total_delivery_fee"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.total_delivery_fee}
                            aria-invalid={!!errors.total_delivery_fee}
                            onChange={(event) => setData('total_delivery_fee', event.target.value)}
                        />
                        <FieldDescription>Lalamove / rider fee for the whole trip.</FieldDescription>
                        <InputError message={errors.total_delivery_fee} />
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
                        <FieldDescription>Use “Settle” to finalise the fees.</FieldDescription>
                        <InputError message={errors.status} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="allocations">Orders in this pool</FieldLabel>
                        <RepeaterField
                            name="allocations"
                            rows={data.allocations}
                            columns={[
                                { name: 'order_id', label: 'Order', type: 'select', options: options.order_options ?? [], width: '3fr' },
                                { name: 'assigned_fee', label: 'Shipping share (₱)', type: 'number', step: '0.01', min: 0, width: '10rem' },
                            ]}
                            onChange={(rows) => setData('allocations', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add order"
                        />
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
