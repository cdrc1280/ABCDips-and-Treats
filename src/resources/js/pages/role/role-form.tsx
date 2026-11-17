import { ShieldCheck } from 'lucide-react';

import { Field, FieldDescription, FieldLabel, InputError, fieldGrid } from '@/components/custom/field';
import { FormSection } from '@/components/custom/form-section';
import { MultiSelect } from '@/components/custom/multi-select';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import type { Option } from '@/types';

export interface RoleFormData {
    name: string;
    permissions: string[];
}

export type RoleFormOptions = { permission_options?: Option<string>[] };

interface Props {
    data: RoleFormData;
    setData: <K extends keyof RoleFormData>(key: K, value: RoleFormData[K]) => void;
    errors: Partial<Record<keyof RoleFormData, string>>;
    options: RoleFormOptions;
}

/** Presentational: create.tsx / edit.tsx own the state. */
export function RoleForm({ data, setData, errors, options }: Props) {
    void options;
    return (
        <Card className="gap-0 py-0">
            <FormSection icon={ShieldCheck} title="Details" description="" fields={['name', 'permissions']} errors={errors}>
                <div className={fieldGrid}>
                    <Field>
                        <FieldLabel name="name">Role name</FieldLabel>
                        <Input
                            id="name"
                            type="text"
                            value={data.name}
                            aria-invalid={!!errors.name}
                            onChange={(event) => setData('name', event.target.value)}
                        />
                        <FieldDescription>Lowercase, e.g. pastry_lead.</FieldDescription>
                        <InputError message={errors.name} />
                    </Field>
                    <Field>
                        <FieldLabel name="permissions">Permissions</FieldLabel>
                        <MultiSelect
                            id="permissions"
                            value={data.permissions}
                            options={options.permission_options ?? []}
                            invalid={!!errors.permissions}
                            onChange={(value) => setData('permissions', value)}
                        />
                        <FieldDescription>Super admins always have every permission.</FieldDescription>
                        <InputError message={errors.permissions} />
                    </Field>
                </div>
            </FormSection>
        </Card>
    );
}
