import { KeyRound, UserCog } from 'lucide-react';

import { PasswordInput } from '@/components/custom/password-input';
import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { MultiSelect } from '@/components/custom/multi-select';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { Option } from '@/types';

export interface UserFormData {
    name: string;
    email: string;
    phone: string;
    roles: string[];
    password: string;
    password_confirmation: string;
}

export type UserFormOptions = { role_options?: Option<string>[] };

interface Props {
    data: UserFormData;
    setData: <K extends keyof UserFormData>(key: K, value: UserFormData[K]) => void;
    errors: Partial<Record<keyof UserFormData, string>>;
    options: UserFormOptions;
}

/** Presentational: create.tsx / edit.tsx own the state. */
export function UserForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={UserCog} title="Details" description="" fields={['name', 'email', 'phone', 'roles']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Full name</FieldLabel>
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
                    <Field>
                        <FieldLabel name="roles">Roles</FieldLabel>
                        <MultiSelect
                            id="roles"
                            value={data.roles}
                            options={options.role_options ?? []}
                            invalid={!!errors.roles}
                            onChange={(value) => setData('roles', value)}
                        />
                        <FieldDescription>Permissions come from roles — manage them under Roles.</FieldDescription>
                        <InputError message={errors.roles} />
                    </Field>
                </div>
            </FormSection>
            <FormSection icon={KeyRound} title="Sign-in" description="" fields={['password', 'password_confirmation']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="password">Password</FieldLabel>
                        <PasswordInput
                            id="password"
                            value={data.password}
                            aria-invalid={!!errors.password}
                            onChange={(event) => setData('password', event.target.value)}
                        />
                        <FieldDescription>Leave empty to keep the current password.</FieldDescription>
                        <InputError message={errors.password} />
                    </Field>
                    <Field>
                        <FieldLabel name="password_confirmation">Confirm password</FieldLabel>
                        <PasswordInput
                            id="password_confirmation"
                            value={data.password_confirmation}
                            aria-invalid={!!errors.password_confirmation}
                            onChange={(event) => setData('password_confirmation', event.target.value)}
                        />
                        <InputError message={errors.password_confirmation} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
