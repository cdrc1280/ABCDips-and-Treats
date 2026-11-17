import { Calculator, ChefHat, PackageOpen, Wheat } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface RecipeFormData {
    name: string;
    product_id: string;
    yield_qty: string;
    prep_time_minutes: string;
    baking_time_minutes: string;
    instructions: string;
    ingredients: Record<string, string>[];
    packaging: Record<string, string>[];
    overhead_pct: string;
    markup_pct: string;
    labor_pct: string;
}

export type RecipeFormOptions = { product_options?: Option<string>[]; ingredient_options?: Option<string>[]; packaging_options?: Option<string>[] };

interface Props {
    data: RecipeFormData;
    setData: <K extends keyof RecipeFormData>(key: K, value: RecipeFormData[K]) => void;
    errors: Partial<Record<keyof RecipeFormData, string>>;
    options: RecipeFormOptions;
}

/** Presentational: create.tsx / edit.tsx own the state. */
export function RecipeForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={ChefHat}
                title="Details"
                description=""
                fields={['name', 'product_id', 'yield_qty', 'prep_time_minutes', 'baking_time_minutes', 'instructions']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Recipe name</FieldLabel>
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
                        <FieldLabel name="product_id">Product</FieldLabel>
                        <SelectDropdown<string>
                            id="product_id"
                            value={data.product_id ? String(data.product_id) : null}
                            options={options.product_options ?? []}
                            invalid={!!errors.product_id}
                            onChange={(value) => setData('product_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.product_id} />
                    </Field>
                    <Field>
                        <FieldLabel name="yield_qty">Yield per batch</FieldLabel>
                        <Input
                            id="yield_qty"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={1}
                            value={data.yield_qty}
                            aria-invalid={!!errors.yield_qty}
                            onChange={(event) => setData('yield_qty', event.target.value)}
                        />
                        <FieldDescription>Pieces one batch makes.</FieldDescription>
                        <InputError message={errors.yield_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="prep_time_minutes">Prep time (min)</FieldLabel>
                        <Input
                            id="prep_time_minutes"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={0}
                            value={data.prep_time_minutes}
                            aria-invalid={!!errors.prep_time_minutes}
                            onChange={(event) => setData('prep_time_minutes', event.target.value)}
                        />
                        <InputError message={errors.prep_time_minutes} />
                    </Field>
                    <Field>
                        <FieldLabel name="baking_time_minutes">Baking time (min)</FieldLabel>
                        <Input
                            id="baking_time_minutes"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={0}
                            value={data.baking_time_minutes}
                            aria-invalid={!!errors.baking_time_minutes}
                            onChange={(event) => setData('baking_time_minutes', event.target.value)}
                        />
                        <InputError message={errors.baking_time_minutes} />
                    </Field>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="instructions">Method</FieldLabel>
                        <Textarea
                            id="instructions"
                            rows={10}
                            value={data.instructions}
                            aria-invalid={!!errors.instructions}
                            onChange={(event) => setData('instructions', event.target.value)}
                        />
                        <InputError message={errors.instructions} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Wheat}
                title="Ingredients"
                description="Quantities for one batch; units are converted to each ingredient’s stock unit."
                fields={['ingredients']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="ingredients">Ingredients</FieldLabel>
                        <RepeaterField
                            name="ingredients"
                            rows={data.ingredients}
                            columns={[
                                {
                                    name: 'ingredient_id',
                                    label: 'Ingredient',
                                    type: 'select',
                                    options: options.ingredient_options ?? [],
                                    width: '2fr',
                                },
                                { name: 'qty_required', label: 'Quantity', type: 'number', step: '0.001', min: 0, width: '8rem' },
                                {
                                    name: 'unit',
                                    label: 'Unit',
                                    type: 'select',
                                    options: [
                                        { value: 'grams', label: 'Grams (g)' },
                                        { value: 'kg', label: 'Kilograms (kg)' },
                                        { value: 'ml', label: 'Milliliters (ml)' },
                                        { value: 'L', label: 'Liters (L)' },
                                        { value: 'piece', label: 'Piece (pcs)' },
                                        { value: 'box', label: 'Box / pack' },
                                    ],
                                    width: '9rem',
                                },
                            ]}
                            onChange={(rows) => setData('ingredients', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add ingredient"
                        />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={PackageOpen} title="Packaging" description="" fields={['packaging']} errors={errors}>
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="packaging">Packaging</FieldLabel>
                        <RepeaterField
                            name="packaging"
                            rows={data.packaging}
                            columns={[
                                {
                                    name: 'packaging_material_id',
                                    label: 'Item',
                                    type: 'select',
                                    options: options.packaging_options ?? [],
                                    width: '2fr',
                                },
                                { name: 'name', label: 'Label', width: '2fr' },
                                { name: 'unit', label: 'Unit', placeholder: 'pcs', width: '6rem' },
                                { name: 'package_qty', label: 'Pack qty', type: 'number', step: '0.001', min: 0, width: '7rem' },
                                { name: 'package_cost', label: 'Pack cost', type: 'number', step: '0.01', min: 0, width: '8rem' },
                                { name: 'qty_used', label: 'Used', type: 'number', step: '0.001', min: 0, width: '7rem' },
                            ]}
                            onChange={(rows) => setData('packaging', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add packaging"
                        />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Calculator}
                title="Pricing"
                description="Overhead on raw materials, then markup and labor on total cost (the bakery’s costing sheet)."
                fields={['overhead_pct', 'markup_pct', 'labor_pct']}
                errors={errors}
            >
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
