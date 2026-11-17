import { Form, Head, Link } from '@inertiajs/react';
import { Loader2, MailCheck } from 'lucide-react';

import { AuthHeading } from '@/components/custom/auth-heading';
import { Button } from '@/components/ui/button';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

export default function VerifyEmail({ status }: { status?: string }) {
    return (
        <>
            <Head title="Verify your email" />
            <MailCheck className="mb-4 size-10 text-primary" />
            <AuthHeading
                title="Check your inbox"
                description="We sent a verification link to your email. Click it to finish setting up your account — you'll need it to place orders."
            />
            {status === 'verification-link-sent' && (
                <div className="mb-4 rounded-lg border border-success/30 bg-success/10 px-3 py-2 text-sm text-success-ink">
                    A new verification link has been sent.
                </div>
            )}
            <Form {...send.form()} className="grid gap-3">
                {({ processing }) => (
                    <>
                        <Button type="submit" disabled={processing}>
                            {processing && <Loader2 className="animate-spin" />} Resend verification email
                        </Button>
                        <Button variant="ghost" asChild>
                            <Link href={logout().url} method="post" as="button">
                                Sign out
                            </Link>
                        </Button>
                    </>
                )}
            </Form>
        </>
    );
}
