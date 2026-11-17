import { Form, Head } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';

import { PasswordInput } from '@/components/custom/password-input';
import { AuthHeading } from '@/components/custom/auth-heading';
import { Field, InputError } from '@/components/custom/field';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/password/confirm';

export default function ConfirmPassword() {
    return (
        <>
            <Head title="Confirm password" />
            <AuthHeading title="Confirm your password" description="This is a secure area. Please confirm your password to continue." />
            <Form {...store.form()} resetOnSuccess={['password']} className="grid gap-4">
                {({ processing, errors }) => (
                    <>
                        <Field>
                            <Label htmlFor="password">Password</Label>
                            <PasswordInput
                                id="password"
                                name="password"
                                required
                                autoFocus
                                autoComplete="current-password"
                                aria-invalid={!!errors.password}
                            />
                            <InputError message={errors.password} />
                        </Field>
                        <Button type="submit" className="w-full" disabled={processing}>
                            {processing && <Loader2 className="animate-spin" />} Confirm
                        </Button>
                    </>
                )}
            </Form>
        </>
    );
}
