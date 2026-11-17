import { CalendarCheck, Clock } from 'lucide-react';

import { DatePicker } from '@/components/custom/date-picker';
import { Field, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import type { Option } from '@/types';

export interface AttendanceFormData {
    employee_id: string;
    date: string;
    status: string;
    time_in: string;
    time_out: string;
    hours_worked: string;
    is_overtime: boolean;
    overtime_hours: string;
    is_holiday: boolean;
    notes: string;
}

export type AttendanceFormOptions = { employee_options?: Option<string>[] };

interface Props {
    data: AttendanceFormData;
    setData: <K extends keyof AttendanceFormData>(key: K, value: AttendanceFormData[K]) => void;
    errors: Partial<Record<keyof AttendanceFormData, string>>;
    options: AttendanceFormOptions;
}

const statusOptions = [
    { value: 'present', label: 'Present' },
    { value: 'absent', label: 'Absent' },
    { value: 'late', label: 'Late' },
    { value: 'half_day', label: 'Half day' },
    { value: 'on_leave', label: 'On leave' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function AttendanceForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={CalendarCheck} title="Details" description="" fields={['employee_id', 'date', 'status']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="employee_id">Employee</FieldLabel>
                        <SelectDropdown<string>
                            id="employee_id"
                            value={data.employee_id ? String(data.employee_id) : null}
                            options={options.employee_options ?? []}
                            invalid={!!errors.employee_id}
                            onChange={(value) => setData('employee_id', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.employee_id} />
                    </Field>
                    <Field>
                        <FieldLabel name="date">Date</FieldLabel>
                        <DatePicker id="date" value={data.date || null} invalid={!!errors.date} onChange={(value) => setData('date', value ?? '')} />
                        <InputError message={errors.date} />
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
                </div>
            </FormSection>
            <FormSection
                icon={Clock}
                title="Hours"
                description=""
                fields={['time_in', 'time_out', 'hours_worked', 'is_overtime', 'overtime_hours', 'is_holiday', 'notes']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="time_in">Time in</FieldLabel>
                        <Input
                            id="time_in"
                            type="time"
                            value={data.time_in}
                            aria-invalid={!!errors.time_in}
                            onChange={(event) => setData('time_in', event.target.value)}
                        />
                        <InputError message={errors.time_in} />
                    </Field>
                    <Field>
                        <FieldLabel name="time_out">Time out</FieldLabel>
                        <Input
                            id="time_out"
                            type="time"
                            value={data.time_out}
                            aria-invalid={!!errors.time_out}
                            onChange={(event) => setData('time_out', event.target.value)}
                        />
                        <InputError message={errors.time_out} />
                    </Field>
                    <Field>
                        <FieldLabel name="hours_worked">Hours worked</FieldLabel>
                        <Input
                            id="hours_worked"
                            type="number"
                            inputMode="decimal"
                            step="0.25"
                            min={0}
                            value={data.hours_worked}
                            aria-invalid={!!errors.hours_worked}
                            onChange={(event) => setData('hours_worked', event.target.value)}
                        />
                        <InputError message={errors.hours_worked} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_overtime">Overtime</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_overtime" checked={data.is_overtime} onCheckedChange={(checked) => setData('is_overtime', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_overtime ? 'Has overtime' : 'No overtime'}</span>
                        </div>
                        <InputError message={errors.is_overtime} />
                    </Field>
                    <Field>
                        <FieldLabel name="overtime_hours">Overtime hours</FieldLabel>
                        <Input
                            id="overtime_hours"
                            type="number"
                            inputMode="decimal"
                            step="0.25"
                            min={0}
                            value={data.overtime_hours}
                            aria-invalid={!!errors.overtime_hours}
                            onChange={(event) => setData('overtime_hours', event.target.value)}
                        />
                        <InputError message={errors.overtime_hours} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_holiday">Holiday</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_holiday" checked={data.is_holiday} onCheckedChange={(checked) => setData('is_holiday', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_holiday ? 'Holiday pay applies' : 'Regular day'}</span>
                        </div>
                        <InputError message={errors.is_holiday} />
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
