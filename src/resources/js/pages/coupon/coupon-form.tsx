import { DateTimePicker } from '@/components/custom/date-picker';
import { CalendarClock, Gauge, TicketPercent } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';

export interface CouponFormData {
    code: string;
    type: string;
    value: string;
    min_spend: string;
    max_discount: string;
    usage_limit: string;
    starts_at: string;
    expires_at: string;
    is_active: boolean;
}

export type CouponFormOptions = Record<string, never>;

interface Props {
    data: CouponFormData;
    setData: <K extends keyof CouponFormData>(key: K, value: CouponFormData[K]) => void;
    errors: Partial<Record<keyof CouponFormData, string>>;
    options: CouponFormOptions;
}

const typeOptions = [
    { value: 'fixed', label: 'Fixed amount (₱)' },
    { value: 'percent', label: 'Percentage (%)' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function CouponForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={TicketPercent} title="Details" description="" fields={['code', 'type', 'value']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="code">Code</FieldLabel>
                        <Input
                            id="code"
                            type="text"
                            value={data.code}
                            aria-invalid={!!errors.code}
                            onChange={(event) => setData('code', event.target.value)}
                            placeholder="SWEET10"
                        />
                        <InputError message={errors.code} />
                    </Field>
                    <Field>
                        <FieldLabel name="type">Discount type</FieldLabel>
                        <SelectDropdown
                            id="type"
                            value={data.type || null}
                            options={typeOptions}
                            invalid={!!errors.type}
                            onChange={(value) => setData('type', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.type} />
                    </Field>
                    <Field>
                        <FieldLabel name="value">Value</FieldLabel>
                        <Input
                            id="value"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.value}
                            aria-invalid={!!errors.value}
                            onChange={(event) => setData('value', event.target.value)}
                        />
                        <FieldDescription>Pesos for fixed, percent for percentage.</FieldDescription>
                        <InputError message={errors.value} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={Gauge} title="Limits" description="" fields={['min_spend', 'max_discount', 'usage_limit']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="min_spend">Minimum spend</FieldLabel>
                        <Input
                            id="min_spend"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.min_spend}
                            aria-invalid={!!errors.min_spend}
                            onChange={(event) => setData('min_spend', event.target.value)}
                        />
                        <InputError message={errors.min_spend} />
                    </Field>
                    <Field>
                        <FieldLabel name="max_discount">Maximum discount</FieldLabel>
                        <Input
                            id="max_discount"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.max_discount}
                            aria-invalid={!!errors.max_discount}
                            onChange={(event) => setData('max_discount', event.target.value)}
                        />
                        <FieldDescription>Caps percentage discounts. Leave empty for no cap.</FieldDescription>
                        <InputError message={errors.max_discount} />
                    </Field>
                    <Field>
                        <FieldLabel name="usage_limit">Usage limit</FieldLabel>
                        <Input
                            id="usage_limit"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={1}
                            value={data.usage_limit}
                            aria-invalid={!!errors.usage_limit}
                            onChange={(event) => setData('usage_limit', event.target.value)}
                        />
                        <FieldDescription>Total redemptions allowed. Leave empty for unlimited.</FieldDescription>
                        <InputError message={errors.usage_limit} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={CalendarClock} title="Schedule" description="" fields={['starts_at', 'expires_at', 'is_active']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="starts_at">Starts</FieldLabel>
                        <DateTimePicker
                            id="starts_at"
                            value={data.starts_at}
                            invalid={!!errors.starts_at}
                            onChange={(value) => setData('starts_at', value)}
                        />
                        <InputError message={errors.starts_at} />
                    </Field>
                    <Field>
                        <FieldLabel name="expires_at">Expires</FieldLabel>
                        <DateTimePicker
                            id="expires_at"
                            value={data.expires_at}
                            invalid={!!errors.expires_at}
                            onChange={(value) => setData('expires_at', value)}
                        />
                        <InputError message={errors.expires_at} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_active">Active</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_active" checked={data.is_active} onCheckedChange={(checked) => setData('is_active', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_active ? 'Can be redeemed' : 'Paused'}</span>
                        </div>
                        <InputError message={errors.is_active} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
