import { Form, Head } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';

import { AuthHeading } from '@/components/custom/auth-heading';
import { Field, InputError } from '@/components/custom/field';
import { TextLink } from '@/components/custom/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { email } from '@/routes/password';

export default function ForgotPassword({ status }: { status?: string }) {
    return (
        <>
            <Head title="Forgot password" />
            <AuthHeading title="Reset your password" description="Enter your email and we'll send you a link to choose a new password." />
            {status && <div className="mb-4 rounded-lg border border-success/30 bg-success/10 px-3 py-2 text-sm text-success-ink">{status}</div>}
            <Form {...email.form()} className="grid gap-4">
                {({ processing, errors }) => (
                    <>
                        <Field>
                            <Label htmlFor="email">Email</Label>
                            <Input id="email" type="email" name="email" required autoFocus autoComplete="email" aria-invalid={!!errors.email} />
                            <InputError message={errors.email} />
                        </Field>
                        <Button type="submit" className="w-full" disabled={processing}>
                            {processing && <Loader2 className="animate-spin" />} Email reset link
                        </Button>
                    </>
                )}
            </Form>
            <p className="mt-6 text-center text-sm text-muted-foreground">
                Remembered it? <TextLink href={login().url}>Back to sign in</TextLink>
            </p>
        </>
    );
}
