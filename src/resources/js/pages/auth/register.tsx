import { Form, Head } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';

import { AuthHeading } from '@/components/custom/auth-heading';
import { Field, FieldDescription, InputError } from '@/components/custom/field';
import { TextLink } from '@/components/custom/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { store } from '@/routes/register';

export default function Register() {
    return (
        <>
            <Head title="Create account" />
            <AuthHeading title="Create your account" description="Order faster, track deliveries and save your favourites." />

            <Form {...store.form()} resetOnSuccess={['password', 'password_confirmation']} className="grid gap-4">
                {({ processing, errors }) => (
                    <>
                        <Field>
                            <Label htmlFor="name">Full name</Label>
                            <Input id="name" name="name" required autoFocus autoComplete="name" aria-invalid={!!errors.name} />
                            <InputError message={errors.name} />
                        </Field>
                        <Field>
                            <Label htmlFor="email">Email</Label>
                            <Input id="email" type="email" name="email" required autoComplete="email" aria-invalid={!!errors.email} />
                            <InputError message={errors.email} />
                        </Field>
                        <Field>
                            <Label htmlFor="password">Password</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autoComplete="new-password"
                                aria-invalid={!!errors.password}
                            />
                            <FieldDescription>At least 8 characters with upper and lower case letters, a number and a symbol.</FieldDescription>
                            <InputError message={errors.password} />
                        </Field>
                        <Field>
                            <Label htmlFor="password_confirmation">Confirm password</Label>
                            <Input id="password_confirmation" type="password" name="password_confirmation" required autoComplete="new-password" />
                        </Field>
                        <Button type="submit" className="w-full" disabled={processing}>
                            {processing && <Loader2 className="animate-spin" />} Create account
                        </Button>
                    </>
                )}
            </Form>

            <p className="mt-6 text-center text-sm text-muted-foreground">
                Already have an account? <TextLink href={login().url}>Sign in</TextLink>
            </p>
        </>
    );
}
