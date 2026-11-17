import { ClipboardList, ListOrdered, Receipt } from 'lucide-react';

import { DatePicker } from '@/components/custom/date-picker';
import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface PurchaseOrderFormData {
    po_number: string;
    supplier_id: string;
    status: string;
    expected_delivery_date: string;
    payment_terms: string;
    items: Record<string, string>[];
    tax: string;
    delivery_receipt_no: string;
    sales_invoice_no: string;
    notes: string;
}

export type PurchaseOrderFormOptions = { supplier_options?: Option<string>[]; ingredient_options?: Option<string>[] };

interface Props {
    data: PurchaseOrderFormData;
    setData: <K extends keyof PurchaseOrderFormData>(key: K, value: PurchaseOrderFormData[K]) => void;
    errors: Partial<Record<keyof PurchaseOrderFormData, string>>;
    options: PurchaseOrderFormOptions;
}

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'sent', label: 'Sent' },
    { value: 'cancelled', label: 'Cancelled' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function PurchaseOrderForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={ClipboardList}
                title="Details"
                description=""
                fields={['po_number', 'supplier_id', 'status', 'expected_delivery_date', 'payment_terms']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="po_number">PO number</FieldLabel>
                        <Input
                            id="po_number"
                            type="text"
                            value={data.po_number}
                            aria-invalid={!!errors.po_number}
                            onChange={(event) => setData('po_number', event.target.value)}
                        />
                        <FieldDescription>Leave empty to generate.</FieldDescription>
                        <InputError message={errors.po_number} />
                    </Field>
                    <Field>
                        <FieldLabel name="supplier_id">Supplier</FieldLabel>
                        <SelectDropdown<string>
                            id="supplier_id"
                            value={data.supplier_id ? String(data.supplier_id) : null}
                            options={options.supplier_options ?? []}
                            invalid={!!errors.supplier_id}
                            onChange={(value) => setData('supplier_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.supplier_id} />
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
                        <FieldDescription>Use “Receive” to mark it received — that updates stock.</FieldDescription>
                        <InputError message={errors.status} />
                    </Field>
                    <Field>
                        <FieldLabel name="expected_delivery_date">Expected delivery</FieldLabel>
                        <DatePicker
                            id="expected_delivery_date"
                            value={data.expected_delivery_date || null}
                            invalid={!!errors.expected_delivery_date}
                            onChange={(value) => setData('expected_delivery_date', value ?? '')}
                        />
                        <InputError message={errors.expected_delivery_date} />
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
                        <InputError message={errors.payment_terms} />
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
                                { name: 'qty_ordered', label: 'Qty ordered', type: 'number', step: '0.001', min: 0.001, width: '8rem' },
                                { name: 'unit_cost', label: 'Unit cost', type: 'number', step: '0.01', min: 0, width: '8rem' },
                            ]}
                            onChange={(rows) => setData('items', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add item"
                            summary={(row) => `₱${(Number(row.qty_ordered || 0) * Number(row.unit_cost || 0)).toFixed(2)}`}
                        />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Receipt}
                title="Totals & documents"
                description=""
                fields={['tax', 'delivery_receipt_no', 'sales_invoice_no', 'notes']}
                errors={errors}
            >
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
                    <Field>
                        <FieldLabel name="delivery_receipt_no">Delivery receipt no.</FieldLabel>
                        <Input
                            id="delivery_receipt_no"
                            type="text"
                            value={data.delivery_receipt_no}
                            aria-invalid={!!errors.delivery_receipt_no}
                            onChange={(event) => setData('delivery_receipt_no', event.target.value)}
                        />
                        <InputError message={errors.delivery_receipt_no} />
                    </Field>
                    <Field>
                        <FieldLabel name="sales_invoice_no">Sales invoice no.</FieldLabel>
                        <Input
                            id="sales_invoice_no"
                            type="text"
                            value={data.sales_invoice_no}
                            aria-invalid={!!errors.sales_invoice_no}
                            onChange={(event) => setData('sales_invoice_no', event.target.value)}
                        />
                        <InputError message={errors.sales_invoice_no} />
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
