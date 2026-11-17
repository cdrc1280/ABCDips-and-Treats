import { Banknote, Users } from 'lucide-react';

import { DatePicker } from '@/components/custom/date-picker';
import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { RepeaterField } from '@/components/custom/repeater-field';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface PayrollFormData {
    payroll_number: string;
    period_start: string;
    period_end: string;
    status: string;
    notes: string;
    items: Record<string, string>[];
}

export type PayrollFormOptions = { employee_options?: Option<string>[] };

interface Props {
    data: PayrollFormData;
    setData: <K extends keyof PayrollFormData>(key: K, value: PayrollFormData[K]) => void;
    errors: Partial<Record<keyof PayrollFormData, string>>;
    options: PayrollFormOptions;
}

const statusOptions = [
    { value: 'draft', label: 'Draft' },
    { value: 'approved', label: 'Approved' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function PayrollForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={Banknote}
                title="Details"
                description=""
                fields={['payroll_number', 'period_start', 'period_end', 'status', 'notes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="payroll_number">Payroll number</FieldLabel>
                        <Input
                            id="payroll_number"
                            type="text"
                            value={data.payroll_number}
                            aria-invalid={!!errors.payroll_number}
                            onChange={(event) => setData('payroll_number', event.target.value)}
                        />
                        <FieldDescription>Generated automatically.</FieldDescription>
                        <InputError message={errors.payroll_number} />
                    </Field>
                    <Field>
                        <FieldLabel name="period_start">Period start</FieldLabel>
                        <DatePicker
                            id="period_start"
                            value={data.period_start || null}
                            invalid={!!errors.period_start}
                            onChange={(value) => setData('period_start', value ?? '')}
                        />
                        <InputError message={errors.period_start} />
                    </Field>
                    <Field>
                        <FieldLabel name="period_end">Period end</FieldLabel>
                        <DatePicker
                            id="period_end"
                            value={data.period_end || null}
                            invalid={!!errors.period_end}
                            onChange={(value) => setData('period_end', value ?? '')}
                        />
                        <InputError message={errors.period_end} />
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
                        <FieldDescription>Use “Mark paid” to close the run.</FieldDescription>
                        <InputError message={errors.status} />
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
            <FormSection icon={Users} title="Pay lines" description="" fields={['items']} errors={errors}>
                <div className={fieldGrid}>
                    <Field className="sm:col-span-2 2xl:col-span-3">
                        <FieldLabel name="items">Pay lines</FieldLabel>
                        <FieldDescription>
                            Gross = basic − attendance (absences, lateness) + overtime + holiday + bonuses. Net = gross − SSS, PhilHealth, Pag-IBIG
                            and tax. New payrolls fill these from attendance using the pay policy in Settings; totals are recalculated on save.
                        </FieldDescription>
                        <RepeaterField
                            name="items"
                            rows={data.items}
                            columns={[
                                { name: 'employee_id', label: 'Employee', type: 'select', options: options.employee_options ?? [], width: '2fr' },
                                { name: 'basic_pay', label: 'Basic', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'attendance_deduction', label: 'Attendance −', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'overtime_pay', label: 'Overtime', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'holiday_pay', label: 'Holiday', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'bonuses', label: 'Bonuses', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'sss_deduction', label: 'SSS', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'philhealth_deduction', label: 'PhilHealth', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'pagibig_deduction', label: 'Pag-IBIG', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'withholding_tax', label: 'Tax', type: 'number', step: '0.01', min: 0, width: '7rem' },
                                { name: 'attendance_note', label: 'Attendance note', type: 'text', width: '12rem' },
                            ]}
                            onChange={(rows) => setData('items', rows)}
                            errors={errors as Record<string, string | undefined>}
                            addLabel="Add employee"
                            summary={(row) => {
                                const n = (k: string) => Number(row[k] || 0);
                                // Same formula as App\Domains\Employee\Support\PayLine.
                                const gross =
                                    Math.max(0, n('basic_pay') - n('attendance_deduction')) + n('overtime_pay') + n('holiday_pay') + n('bonuses');
                                const net = gross - n('sss_deduction') - n('philhealth_deduction') - n('pagibig_deduction') - n('withholding_tax');
                                return `₱${Math.max(0, net).toFixed(2)}`;
                            }}
                        />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
