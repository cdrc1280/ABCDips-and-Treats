import { Form, Head } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';

import { AuthHeading } from '@/components/custom/auth-heading';
import { Field, InputError } from '@/components/custom/field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/password';

export default function ResetPassword({ token, email }: { token: string; email: string }) {
    return (
        <>
            <Head title="Choose a new password" />
            <AuthHeading title="Choose a new password" />
            <Form
                {...update.form()}
                transform={(data) => ({ ...data, token, email })}
                resetOnSuccess={['password', 'password_confirmation']}
                className="grid gap-4"
            >
                {({ processing, errors }) => (
                    <>
                        <Field>
                            <Label htmlFor="email">Email</Label>
                            <Input id="email" type="email" value={email} readOnly />
                            <InputError message={errors.email} />
                        </Field>
                        <Field>
                            <Label htmlFor="password">New password</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autoFocus
                                autoComplete="new-password"
                                aria-invalid={!!errors.password}
                            />
                            <InputError message={errors.password} />
                        </Field>
                        <Field>
                            <Label htmlFor="password_confirmation">Confirm password</Label>
                            <Input id="password_confirmation" type="password" name="password_confirmation" required autoComplete="new-password" />
                        </Field>
                        <Button type="submit" className="w-full" disabled={processing}>
                            {processing && <Loader2 className="animate-spin" />} Save new password
                        </Button>
                    </>
                )}
            </Form>
        </>
    );
}
