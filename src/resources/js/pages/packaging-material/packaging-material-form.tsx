import { Package, PackageOpen } from 'lucide-react';

import { Field, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

export interface PackagingMaterialFormData {
    name: string;
    sku: string;
    type: string;
    unit: string;
    cost_per_unit: string;
    stock_qty: string;
    min_stock_qty: string;
    is_active: boolean;
    notes: string;
}

export type PackagingMaterialFormOptions = Record<string, never>;

interface Props {
    data: PackagingMaterialFormData;
    setData: <K extends keyof PackagingMaterialFormData>(key: K, value: PackagingMaterialFormData[K]) => void;
    errors: Partial<Record<keyof PackagingMaterialFormData, string>>;
    options: PackagingMaterialFormOptions;
}

const typeOptions = [
    { value: 'box', label: 'Box' },
    { value: 'bag', label: 'Bag' },
    { value: 'board', label: 'Board' },
    { value: 'container', label: 'Container' },
    { value: 'ribbon', label: 'Ribbon' },
    { value: 'label', label: 'Label' },
    { value: 'sticker', label: 'Sticker' },
    { value: 'tape', label: 'Tape' },
    { value: 'wrap', label: 'Wrap' },
    { value: 'other', label: 'Other' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function PackagingMaterialForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={PackageOpen} title="Details" description="" fields={['name', 'sku', 'type', 'unit']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Name</FieldLabel>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            aria-invalid={!!errors.name}
                            onChange={(event) => setData('name', event.target.value)}
                            placeholder="e.g. 8-inch cake box"
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
                        <FieldLabel name="type">Type</FieldLabel>
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
                        <FieldLabel name="unit">Unit</FieldLabel>
                        <Input
                            id="unit"
                            type="text"
                            value={data.unit}
                            aria-invalid={!!errors.unit}
                            onChange={(event) => setData('unit', event.target.value)}
                        />
                        <InputError message={errors.unit} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Package}
                title="Stock & cost"
                description=""
                fields={['cost_per_unit', 'stock_qty', 'min_stock_qty', 'is_active', 'notes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="cost_per_unit">Cost per unit</FieldLabel>
                        <Input
                            id="cost_per_unit"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.cost_per_unit}
                            aria-invalid={!!errors.cost_per_unit}
                            onChange={(event) => setData('cost_per_unit', event.target.value)}
                        />
                        <InputError message={errors.cost_per_unit} />
                    </Field>
                    <Field>
                        <FieldLabel name="stock_qty">In stock</FieldLabel>
                        <Input
                            id="stock_qty"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
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
                            step="0.01"
                            min={0}
                            value={data.min_stock_qty}
                            aria-invalid={!!errors.min_stock_qty}
                            onChange={(event) => setData('min_stock_qty', event.target.value)}
                        />
                        <InputError message={errors.min_stock_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_active">Active</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_active" checked={data.is_active} onCheckedChange={(checked) => setData('is_active', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_active ? 'In use' : 'Retired'}</span>
                        </div>
                        <InputError message={errors.is_active} />
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
