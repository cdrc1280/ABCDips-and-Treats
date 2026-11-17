import { Calculator, ListOrdered, Percent } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { Option } from '@/types';

export interface ProductCostingFormData {
    name: string;
    product_id: string;
    yield_qty: string;
    yield_unit: string;
    items: Record<string, string>[];
    overhead_pct: string;
    markup_pct: string;
    labor_pct: string;
}

export type ProductCostingFormOptions = {
    product_options?: Option<string>[];
    ingredient_options?: Option<string>[];
    packaging_options?: Option<string>[];
};

interface Props {
    data: ProductCostingFormData;
    setData: <K extends keyof ProductCostingFormData>(key: K, value: ProductCostingFormData[K]) => void;
    errors: Partial<Record<keyof ProductCostingFormData, string>>;
    options: ProductCostingFormOptions;
}

/** Presentational: create.tsx / edit.tsx own the state. */
export function ProductCostingForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={Calculator} title="Details" description="" fields={['name', 'product_id', 'yield_qty', 'yield_unit']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Sheet name</FieldLabel>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            aria-invalid={!!errors.name}
                            onChange={(event) => setData('name', event.target.value)}
                        />
                        <FieldDescription>Defaults to the product name.</FieldDescription>
                        <InputError message={errors.name} />
                    </Field>
                    <Field>
                        <FieldLabel name="product_id">Product</FieldLabel>
                        <SelectDropdown<string>
                            id="product_id"
                            clearable
                            value={data.product_id ? String(data.product_id) : null}
                            options={options.product_options ?? []}
                            invalid={!!errors.product_id}
                            onChange={(value) => setData('product_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.product_id} />
                    </Field>
                    <Field>
                        <FieldLabel name="yield_qty">Yield</FieldLabel>
                        <Input
                            id="yield_qty"
                            type="number"
                            inputMode="decimal"
                            step="0.001"
                            min={0.001}
                            value={data.yield_qty}
                            aria-invalid={!!errors.yield_qty}
                            onChange={(event) => setData('yield_qty', event.target.value)}
                        />
                        <InputError message={errors.yield_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="yield_unit">Yield unit</FieldLabel>
                        <Input
                            id="yield_unit"
                            type="text"
                            value={data.yield_unit}
                            aria-invalid={!!errors.yield_unit}
                            onChange={(event) => setData('yield_unit', event.target.value)}
                        />
                        <InputError message={errors.yield_unit} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={ListOrdered} title="Cost lines" description="" fields={['items']} errors={errors}>
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="items">Cost lines</FieldLabel>
                        <RepeaterField
                            name="items"
                            rows={data.items}
                            columns={[
                                {
                                    name: 'group',
                                    label: 'Group',
                                    type: 'select',
                                    options: [
                                        { value: 'ingredient', label: 'Ingredient' },
                                        { value: 'packaging', label: 'Packaging' },
                                    ],
                                    width: '8rem',
                                },
                                {
                                    name: 'ingredient_id',
                                    label: 'Ingredient',
                                    type: 'select',
                                    options: options.ingredient_options ?? [],
                                    width: '2fr',
                                },
                                {
                                    name: 'packaging_material_id',
                                    label: 'Packaging',
                                    type: 'select',
                                    options: options.packaging_options ?? [],
                                    width: '2fr',
                                },
                                { name: 'name', label: 'Label', width: '2fr' },
                                { name: 'unit', label: 'Unit', width: '5rem' },
                                { name: 'package_amount', label: 'Pack size', type: 'number', step: '0.001', min: 0, width: '7rem' },
                                { name: 'package_price', label: 'Pack price', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'qty_used', label: 'Used', type: 'number', step: '0.001', min: 0, width: '6rem' },
                            ]}
                            onChange={(rows) => setData('items', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add line"
                        />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={Percent} title="Pricing" description="" fields={['overhead_pct', 'markup_pct', 'labor_pct']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="overhead_pct">Overhead %</FieldLabel>
                        <Input
                            id="overhead_pct"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.overhead_pct}
                            aria-invalid={!!errors.overhead_pct}
                            onChange={(event) => setData('overhead_pct', event.target.value)}
                        />
                        <InputError message={errors.overhead_pct} />
                    </Field>
                    <Field>
                        <FieldLabel name="markup_pct">Markup %</FieldLabel>
                        <Input
                            id="markup_pct"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.markup_pct}
                            aria-invalid={!!errors.markup_pct}
                            onChange={(event) => setData('markup_pct', event.target.value)}
                        />
                        <InputError message={errors.markup_pct} />
                    </Field>
                    <Field>
                        <FieldLabel name="labor_pct">Labor %</FieldLabel>
                        <Input
                            id="labor_pct"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.labor_pct}
                            aria-invalid={!!errors.labor_pct}
                            onChange={(event) => setData('labor_pct', event.target.value)}
                        />
                        <InputError message={errors.labor_pct} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
