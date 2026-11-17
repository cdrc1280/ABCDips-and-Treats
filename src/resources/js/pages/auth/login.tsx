import { Form, Head } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';

import { AuthHeading } from '@/components/custom/auth-heading';
import { Field, InputError } from '@/components/custom/field';
import { TextLink } from '@/components/custom/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

export default function Login({ status, canResetPassword }: { status?: string; canResetPassword: boolean }) {
    return (
        <>
            <Head title="Sign in" />
            <AuthHeading title="Welcome back" description="Sign in to track orders, save favourites and check out faster." />

            {status && <div className="mb-4 rounded-lg border border-success/30 bg-success/10 px-3 py-2 text-sm text-success-ink">{status}</div>}

            <Form {...store.form()} resetOnSuccess={['password']} className="grid gap-4">
                {({ processing, errors }) => (
                    <>
                        <Field>
                            <Label htmlFor="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autoFocus
                                autoComplete="email"
                                placeholder="you@example.com"
                                aria-invalid={!!errors.email}
                            />
                            <InputError message={errors.email} />
                        </Field>
                        <Field>
                            <div className="flex items-center justify-between">
                                <Label htmlFor="password">Password</Label>
                                {canResetPassword && (
                                    <TextLink href={request().url} className="text-xs">
                                        Forgot password?
                                    </TextLink>
                                )}
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autoComplete="current-password"
                                aria-invalid={!!errors.password}
                            />
                            <InputError message={errors.password} />
                        </Field>
                        <label className="flex items-center gap-2 text-sm">
                            <Checkbox name="remember" value="1" /> Keep me signed in
                        </label>
                        <Button type="submit" className="w-full" disabled={processing}>
                            {processing && <Loader2 className="animate-spin" />} Sign in
                        </Button>
                    </>
                )}
            </Form>

            <p className="mt-6 text-center text-sm text-muted-foreground">
                New here? <TextLink href={register().url}>Create an account</TextLink>
            </p>
        </>
    );
}
