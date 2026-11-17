import { CookingPot, Users } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface ProductionBatchFormData {
    batch_number: string;
    recipe_id: string;
    product_id: string;
    planned_qty: string;
    actual_yield_qty: string;
    status: string;
    baker_user_id: string;
    notes: string;
}

export type ProductionBatchFormOptions = { recipe_options?: Option<string>[]; product_options?: Option<string>[]; baker_options?: Option<string>[] };

interface Props {
    data: ProductionBatchFormData;
    setData: <K extends keyof ProductionBatchFormData>(key: K, value: ProductionBatchFormData[K]) => void;
    errors: Partial<Record<keyof ProductionBatchFormData, string>>;
    options: ProductionBatchFormOptions;
}

const statusOptions = [
    { value: 'planned', label: 'Planned' },
    { value: 'in_prep', label: 'In prep' },
    { value: 'baking', label: 'Baking' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function ProductionBatchForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={CookingPot}
                title="Details"
                description=""
                fields={['batch_number', 'recipe_id', 'product_id', 'planned_qty', 'actual_yield_qty']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="batch_number">Batch number</FieldLabel>
                        <Input
                            id="batch_number"
                            type="text"
                            value={data.batch_number}
                            aria-invalid={!!errors.batch_number}
                            onChange={(event) => setData('batch_number', event.target.value)}
                        />
                        <FieldDescription>Leave empty to generate.</FieldDescription>
                        <InputError message={errors.batch_number} />
                    </Field>
                    <Field>
                        <FieldLabel name="recipe_id">Recipe</FieldLabel>
                        <SelectDropdown<string>
                            id="recipe_id"
                            value={data.recipe_id ? String(data.recipe_id) : null}
                            options={options.recipe_options ?? []}
                            invalid={!!errors.recipe_id}
                            onChange={(value) => setData('recipe_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.recipe_id} />
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
                        <FieldLabel name="planned_qty">Planned quantity</FieldLabel>
                        <Input
                            id="planned_qty"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={1}
                            value={data.planned_qty}
                            aria-invalid={!!errors.planned_qty}
                            onChange={(event) => setData('planned_qty', event.target.value)}
                        />
                        <InputError message={errors.planned_qty} />
                    </Field>
                    <Field>
                        <FieldLabel name="actual_yield_qty">Actual yield</FieldLabel>
                        <Input
                            id="actual_yield_qty"
                            type="number"
                            inputMode="decimal"
                            step="1"
                            min={0}
                            value={data.actual_yield_qty}
                            aria-invalid={!!errors.actual_yield_qty}
                            onChange={(event) => setData('actual_yield_qty', event.target.value)}
                        />
                        <FieldDescription>Fill in after baking; defaults to the planned quantity.</FieldDescription>
                        <InputError message={errors.actual_yield_qty} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={Users} title="Team & notes" description="" fields={['status', 'baker_user_id', 'notes']} errors={errors}>
                <div className={fieldGrid}>
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
                        <FieldLabel name="baker_user_id">Baker</FieldLabel>
                        <SelectDropdown<string>
                            id="baker_user_id"
                            clearable
                            value={data.baker_user_id ? String(data.baker_user_id) : null}
                            options={options.baker_options ?? []}
                            invalid={!!errors.baker_user_id}
                            onChange={(value) => setData('baker_user_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.baker_user_id} />
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
