import { Briefcase, IdCard } from 'lucide-react';

import { DatePicker } from '@/components/custom/date-picker';
import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { SelectDropdown } from '@/components/custom/select-dropdown';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import type { Option } from '@/types';

export interface EmployeeFormData {
    first_name: string;
    last_name: string;
    employee_number: string;
    email: string;
    phone: string;
    user_id: string;
    role_title: string;
    employment_type: string;
    basic_monthly_salary: string;
    hired_at: string;
    is_active: boolean;
}

export type EmployeeFormOptions = { user_options?: Option<string>[] };

interface Props {
    data: EmployeeFormData;
    setData: <K extends keyof EmployeeFormData>(key: K, value: EmployeeFormData[K]) => void;
    errors: Partial<Record<keyof EmployeeFormData, string>>;
    options: EmployeeFormOptions;
}

const roleTitleOptions = [
    { value: 'Head Baker', label: 'Head Baker' },
    { value: 'Assistant Baker', label: 'Assistant Baker' },
    { value: 'Cashier', label: 'Cashier' },
    { value: 'Pastry Staff', label: 'Pastry Staff' },
    { value: 'Delivery Rider', label: 'Delivery Rider' },
    { value: 'Manager', label: 'Manager' },
];
const employmentTypeOptions = [
    { value: 'full_time', label: 'Full-time regular' },
    { value: 'part_time', label: 'Part-time flex' },
];

/** Presentational: create.tsx / edit.tsx own the state. */
export function EmployeeForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection
                icon={IdCard}
                title="Details"
                description=""
                fields={['first_name', 'last_name', 'employee_number', 'email', 'phone']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="first_name">First name</FieldLabel>
                        <Input
                            id="first_name"
                            type="text"
                            value={data.first_name}
                            aria-invalid={!!errors.first_name}
                            onChange={(event) => setData('first_name', event.target.value)}
                        />
                        <InputError message={errors.first_name} />
                    </Field>
                    <Field>
                        <FieldLabel name="last_name">Last name</FieldLabel>
                        <Input
                            id="last_name"
                            type="text"
                            value={data.last_name}
                            aria-invalid={!!errors.last_name}
                            onChange={(event) => setData('last_name', event.target.value)}
                        />
                        <InputError message={errors.last_name} />
                    </Field>
                    <Field>
                        <FieldLabel name="employee_number">Employee number</FieldLabel>
                        <Input
                            id="employee_number"
                            type="text"
                            value={data.employee_number}
                            aria-invalid={!!errors.employee_number}
                            onChange={(event) => setData('employee_number', event.target.value)}
                        />
                        <FieldDescription>Leave empty to generate (EMP-0001).</FieldDescription>
                        <InputError message={errors.employee_number} />
                    </Field>
                    <Field>
                        <FieldLabel name="email">Email</FieldLabel>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            aria-invalid={!!errors.email}
                            onChange={(event) => setData('email', event.target.value)}
                        />
                        <InputError message={errors.email} />
                    </Field>
                    <Field>
                        <FieldLabel name="phone">Phone</FieldLabel>
                        <Input
                            id="phone"
                            type="tel"
                            value={data.phone}
                            aria-invalid={!!errors.phone}
                            onChange={(event) => setData('phone', event.target.value)}
                        />
                        <InputError message={errors.phone} />
                    </Field>
                </div>
            </FormSection>
            <FormSection
                icon={Briefcase}
                title="Employment"
                description=""
                fields={['user_id', 'role_title', 'employment_type', 'basic_monthly_salary', 'hired_at', 'is_active']}
                errors={errors}
            >
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="user_id">Login account</FieldLabel>
                        <SelectDropdown<string>
                            id="user_id"
                            clearable
                            value={data.user_id ? String(data.user_id) : null}
                            options={options.user_options ?? []}
                            invalid={!!errors.user_id}
                            onChange={(value) => setData('user_id', value === null ? '' : String(value))}
                        />
                        <FieldDescription>Link a back-office account, if this person signs in.</FieldDescription>
                        <InputError message={errors.user_id} />
                    </Field>
                    <Field>
                        <FieldLabel name="role_title">Role</FieldLabel>
                        <SelectDropdown
                            id="role_title"
                            value={data.role_title || null}
                            options={roleTitleOptions}
                            invalid={!!errors.role_title}
                            onChange={(value) => setData('role_title', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.role_title} />
                    </Field>
                    <Field>
                        <FieldLabel name="employment_type">Employment type</FieldLabel>
                        <SelectDropdown
                            id="employment_type"
                            value={data.employment_type || null}
                            options={employmentTypeOptions}
                            invalid={!!errors.employment_type}
                            onChange={(value) => setData('employment_type', value === null ? '' : String(value))}
                        />
                        <InputError message={errors.employment_type} />
                    </Field>
                    <Field>
                        <FieldLabel name="basic_monthly_salary">Basic monthly salary</FieldLabel>
                        <Input
                            id="basic_monthly_salary"
                            type="number"
                            inputMode="decimal"
                            step="0.01"
                            min={0}
                            value={data.basic_monthly_salary}
                            aria-invalid={!!errors.basic_monthly_salary}
                            onChange={(event) => setData('basic_monthly_salary', event.target.value)}
                        />
                        <InputError message={errors.basic_monthly_salary} />
                    </Field>
                    <Field>
                        <FieldLabel name="hired_at">Hired on</FieldLabel>
                        <DatePicker
                            id="hired_at"
                            value={data.hired_at || null}
                            invalid={!!errors.hired_at}
                            onChange={(value) => setData('hired_at', value ?? '')}
                        />
                        <InputError message={errors.hired_at} />
                    </Field>
                    <Field>
                        <FieldLabel name="is_active">Active</FieldLabel>
                        <div className="flex h-9 items-center gap-3">
                            <Switch id="is_active" checked={data.is_active} onCheckedChange={(checked) => setData('is_active', checked)} />
                            <span className="text-sm text-muted-foreground">{data.is_active ? 'Employed' : 'Inactive'}</span>
                        </div>
                        <InputError message={errors.is_active} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
