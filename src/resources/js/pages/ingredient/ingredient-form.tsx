import { Calculator, Package, Wheat } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { Option } from '@/types';

export interface IngredientFormData {
    name: string;
    sku: string;
    supplier_id: string;
    unit: string;
    item_unit: string;
    item_price: string;
    stock_qty: string;
    min_stock_qty: string;
    reorder_qty: string;
}

export type IngredientFormOptions = { supplier_options?: Option<string>[] };

interface Props {
    data: IngredientFormData;
    setData: <K extends keyof IngredientFormData>(key: K, value: IngredientFormData[K]) => void;
    errors: Partial<Record<keyof IngredientFormData, string>>;
    options: IngredientFormOptions;
}

const unitOptions = [
    { value: 'grams', label: 'Grams (g)' },
    { value: 'kg', label: 'Kilograms (kg)' },
    { value: 'ml', label: 'Milliliters (ml)' },
    { value: 'L', label: 'Liters (L)' },
    { value: 'piece', label: 'Piece (pcs)' },
    { value: 'box', label: 'Box / pack' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function IngredientForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={Wheat}
                title="Details"
                description="What it is and who supplies it."
                fields={['name', 'sku', 'supplier_id']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Name</FieldLabel>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            aria-invalid={!!errors.name}
                            onChange={(event) => setData('name', event.target.value)}
                            placeholder="e.g. Bread flour"
                        />
                        <InputError message={errors.name} />
                    </Field>
                    <Field>
                        <FieldLabel name="sku">SKU</FieldLabel>
                        <Input
                            id="sku"
                            type="text"
                            value={data.sku}
                            aria-invalid={!!errors.sku}
                            onChange={(event) => setData('sku', event.target.value)}
                        />
                        <InputError message={errors.sku} />
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
                </div>
            </FormSection>
            <FormSection
                icon={Calculator}
                title="Pricing"
                description="Pack size and price drive recipe costing."
                fields={['unit', 'item_unit', 'item_price']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="unit">Stock unit</FieldLabel>
                        <SelectDropdown
                            id="unit"
                            value={data.unit || null}
                            options={unitOptions}
                            invalid={!!errors.unit}
                            onChange={(value) => setData('unit', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.unit} />
                    </Field>
                    <Field>
                        <FieldLabel name="item_unit">Pack size</FieldLabel>
                        <Input
                            id="item_unit"
                            type="number"
                            inputMode="decimal"
                            step="0.001"
                            min={0.001}
                            value={data.item_unit}
                            aria-invalid={!!errors.item_unit}
                            onChange={(event) => setData('item_unit', event.target.value)}
                        />
                        <FieldDescription>How many stock units one purchased pack contains (e.g. 25000 g per sack).</FieldDescription>
                        <InputError message={errors.item_unit} />
                    </Field>
                    <Field>
                        <FieldLabel name="item_price">Pack price</FieldLabel>
                        <Input
                            id="item_price"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.item_price}
                            aria-invalid={!!errors.item_price}
                            onChange={(event) => setData('item_price', event.target.value)}
                        />
                        <FieldDescription>Cost per unit is pack price ÷ pack size.</FieldDescription>
                        <InputError message={errors.item_price} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Package}
                title="Stock"
                description="Current quantity and reorder point."
                fields={['stock_qty', 'min_stock_qty', 'reorder_qty']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="stock_qty">In stock</FieldLabel>
                        <Input
                            id="stock_qty"
                            type="number"
                            inputMode="decimal"
                            step="0.001"
                            min={0}
                            value={data.stock_qty}
                            aria-invalid={!!errors.stock_qty}
                            onChange={(event) => setData('stock_qty', event.target.value)}
                        />
                        <InputError message={errors.stock_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="min_stock_qty">Minimum level</FieldLabel>
                        <Input
                            id="min_stock_qty"
                            type="number"
                            inputMode="decimal"
                            step="0.001"
                            min={0}
                            value={data.min_stock_qty}
                            aria-invalid={!!errors.min_stock_qty}
                            onChange={(event) => setData('min_stock_qty', event.target.value)}
                        />
                        <FieldDescription>At or below this the ingredient shows as low stock.</FieldDescription>
                        <InputError message={errors.min_stock_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="reorder_qty">Reorder quantity</FieldLabel>
                        <Input
                            id="reorder_qty"
                            type="number"
                            inputMode="decimal"
                            step="0.001"
                            min={0}
                            value={data.reorder_qty}
                            aria-invalid={!!errors.reorder_qty}
                            onChange={(event) => setData('reorder_qty', event.target.value)}
                        />
                        <InputError message={errors.reorder_qty} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
