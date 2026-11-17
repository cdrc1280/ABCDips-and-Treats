import { FileSpreadsheet, ListOrdered, Receipt } from 'lucide-react';

import { DatePicker } from '@/components/custom/date-picker';
import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface QuotationFormData {
    quotation_number: string;
    supplier_id: string;
    client_name: string;
    status: string;
    quotation_date: string;
    valid_until: string;
    items: Record<string, string>[];
    tax: string;
    notes: string;
}

export type QuotationFormOptions = { supplier_options?: Option<string>[]; ingredient_options?: Option<string>[] };

interface Props {
    data: QuotationFormData;
    setData: <K extends keyof QuotationFormData>(key: K, value: QuotationFormData[K]) => void;
    errors: Partial<Record<keyof QuotationFormData, string>>;
    options: QuotationFormOptions;
}

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'sent', label: 'Sent' },
    { value: 'accepted', label: 'Accepted' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'converted_to_po', label: 'Converted to PO' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function QuotationForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={FileSpreadsheet}
                title="Details"
                description=""
                fields={['quotation_number', 'supplier_id', 'client_name', 'status', 'quotation_date', 'valid_until']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="quotation_number">Quotation number</FieldLabel>
                        <Input
                            id="quotation_number"
                            type="text"
                            value={data.quotation_number}
                            aria-invalid={!!errors.quotation_number}
                            onChange={(event) => setData('quotation_number', event.target.value)}
                        />
                        <FieldDescription>Leave empty to generate.</FieldDescription>
                        <InputError message={errors.quotation_number} />
                    </Field>
                    <Field>
                        <FieldLabel name="supplier_id">Supplier</FieldLabel>
                        <SelectDropdown<string>
                            id="supplier_id"
                            clearable
                            value={data.supplier_id ? String(data.supplier_id) : null}
                            options={options.supplier_options ?? []}
                            invalid={!!errors.supplier_id}
                            onChange={(value) => setData('supplier_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.supplier_id} />
                    </Field>
                    <Field>
                        <FieldLabel name="client_name">Contact / client name</FieldLabel>
                        <Input
                            id="client_name"
                            type="text"
                            value={data.client_name}
                            aria-invalid={!!errors.client_name}
                            onChange={(event) => setData('client_name', event.target.value)}
                        />
                        <InputError message={errors.client_name} />
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
                    <Field>
                        <FieldLabel name="quotation_date">Quote date</FieldLabel>
                        <DatePicker
                            id="quotation_date"
                            value={data.quotation_date || null}
                            invalid={!!errors.quotation_date}
                            onChange={(value) => setData('quotation_date', value ?? '')}
                        />
                        <InputError message={errors.quotation_date} />
                    </Field>
                    <Field>
                        <FieldLabel name="valid_until">Valid until</FieldLabel>
                        <DatePicker
                            id="valid_until"
                            value={data.valid_until || null}
                            invalid={!!errors.valid_until}
                            onChange={(value) => setData('valid_until', value ?? '')}
                        />
                        <InputError message={errors.valid_until} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={ListOrdered} title="Items" description="" fields={['items']} errors={errors}>
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="items">Items</FieldLabel>
                        <RepeaterField
                            name="items"
                            rows={data.items}
                            columns={[
                                {
                                    name: 'ingredient_id',
                                    label: 'Ingredient',
                                    type: 'select',
                                    options: options.ingredient_options ?? [],
                                    width: '2fr',
                                },
                                { name: 'item_description', label: 'Description', width: '2fr' },
                                { name: 'qty', label: 'Qty', type: 'number', step: '0.001', min: 0, width: '7rem' },
                                { name: 'unit', label: 'Unit', width: '6rem' },
                                { name: 'unit_price', label: 'Unit price', type: 'number', step: '0.01', min: 0, width: '8rem' },
                            ]}
                            onChange={(rows) => setData('items', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add item"
                            summary={(row) => `₱${(Number(row.qty || 0) * Number(row.unit_price || 0)).toFixed(2)}`}
                        />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={Receipt} title="Totals" description="" fields={['tax', 'notes']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="tax">Tax</FieldLabel>
                        <Input
                            id="tax"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.tax}
                            aria-invalid={!!errors.tax}
                            onChange={(event) => setData('tax', event.target.value)}
                        />
                        <InputError message={errors.tax} />
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
