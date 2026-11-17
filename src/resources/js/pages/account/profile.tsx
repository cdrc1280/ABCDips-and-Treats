import { Head, router, useForm } from '@inertiajs/react';
import { Camera, MailCheck, MailWarning } from 'lucide-react';

import { PasswordInput } from '@/components/custom/password-input';
import { AccountShell } from '@/components/storefront/account-shell';
import { PsgcAddressFields, type PsgcAddress } from '@/components/storefront/psgc-address-fields';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { initials } from '@/lib/format';
import userPassword from '@/routes/user-password';
import verification from '@/routes/verification';

interface Profile extends PsgcAddress {
    name: string;
    email: string;
    phone: string | null;
    address: string | null;
    email_verified: boolean;
    avatar_url: string | null;
}

function Card({ title, description, children }: { title: string; description?: string; children: React.ReactNode }) {
    return (
        <section className="rounded-2xl border border-primary/20 bg-card p-6 shadow-sm">
            <h2 className="text-lg font-bold text-foreground">{title}</h2>
            {description && <p className="mb-5 text-sm text-muted-foreground">{description}</p>}
            {children}
        </section>
    );
}

function FieldError({ message }: { message?: string }) {
    return message ? <p className="text-xs text-destructive">{message}</p> : null;
}

export default function AccountProfile({ profile, status }: { profile: Profile; status?: string | null }) {
    const details = useForm({
        name: profile.name,
        email: profile.email,
        phone: profile.phone ?? '',
        region: profile.region ?? '',
        province: profile.province ?? '',
        city: profile.city ?? '',
        barangay: profile.barangay ?? '',
        street_address: profile.street_address ?? '',
    });
    const password = useForm({ current_password: '', password: '', password_confirmation: '' });

    return (
        <AccountShell>
            <Head title="My Profile" />
            <div className="space-y-6">
                <Card title="Profile">
                    <div className="mt-4 mb-6 flex items-center gap-4">
                        <div className="relative flex size-20 items-center justify-center overflow-hidden rounded-full bg-highlight/30 text-xl font-semibold text-primary">
                            {profile.avatar_url ? <img src={profile.avatar_url} alt="" className="size-full object-cover" /> : initials(profile.name)}
                        </div>
                        <label className="flex cursor-pointer items-center gap-2 rounded-xl border border-primary/30 px-3 py-2 text-xs font-bold text-primary hover:bg-highlight/20">
                            <Camera className="size-4" /> Change photo
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                className="sr-only"
                                onChange={(e) => {
                                    const file = e.target.files?.[0];
                                    if (file) router.post('/account/avatar', { avatar: file }, { forceFormData: true, preserveScroll: true });
                                }}
                            />
                        </label>
                    </div>

                    <form
                        className="space-y-5"
                        onSubmit={(e) => {
                            e.preventDefault();
                            details.put('/account/profile', { preserveScroll: true });
                        }}
                    >
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-1.5">
                                <Label htmlFor="name">Full name</Label>
                                <Input
                                    id="name"
                                    value={details.data.name}
                                    onChange={(e) => details.setData('name', e.target.value)}
                                    required
                                    autoComplete="name"
                                />
                                <FieldError message={details.errors.name} />
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="phone">Mobile number</Label>
                                <Input
                                    type="tel"
                                    id="phone"
                                    value={details.data.phone}
                                    onChange={(e) => details.setData('phone', e.target.value)}
                                    placeholder="09171234567"
                                    autoComplete="tel"
                                />
                                <FieldError message={details.errors.phone} />
                            </div>
                            <div className="space-y-1.5 sm:col-span-2">
                                <Label htmlFor="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={details.data.email}
                                    onChange={(e) => details.setData('email', e.target.value)}
                                    required
                                    autoComplete="email"
                                />
                                <FieldError message={details.errors.email} />
                                {profile.email_verified ? (
                                    <p className="flex items-center gap-1.5 text-xs font-semibold text-success">
                                        <MailCheck className="size-3.5" /> Verified
                                    </p>
                                ) : (
                                    <p className="flex flex-wrap items-center gap-2 text-xs text-warning-ink">
                                        <MailWarning className="size-3.5" /> Not verified yet.
                                        <button
                                            type="button"
                                            className="font-bold underline"
                                            onClick={() => router.post(verification.send().url, {}, { preserveScroll: true })}
                                        >
                                            Send verification link
                                        </button>
                                        {status === 'verification-link-sent' && (
                                            <span className="font-semibold text-success">Link sent — check your inbox.</span>
                                        )}
                                    </p>
                                )}
                            </div>
                        </div>
                        <div>
                            <h3 className="mb-3 text-sm font-bold text-foreground">Default delivery address</h3>
                            <PsgcAddressFields
                                value={details.data}
                                onChange={(next) => details.setData({ ...details.data, ...next })}
                                errors={details.errors}
                            />
                        </div>
                        <Button type="submit" disabled={details.processing}>
                            Save profile
                        </Button>
                    </form>
                </Card>

                <Card title="Password" description="Use at least 8 characters with upper and lower case letters, a number and a symbol.">
                    <form
                        className="grid max-w-md gap-4"
                        onSubmit={(e) => {
                            e.preventDefault();
                            password.put(userPassword.update().url, {
                                errorBag: 'updatePassword',
                                preserveScroll: true,
                                onSuccess: () => password.reset(),
                            });
                        }}
                    >
                        {(
                            [
                                ['current_password', 'Current password', 'current-password'],
                                ['password', 'New password', 'new-password'],
                                ['password_confirmation', 'Confirm new password', 'new-password'],
                            ] as const
                        ).map(([field, label, autoComplete]) => (
                            <div key={field} className="space-y-1.5">
                                <Label htmlFor={field}>{label}</Label>
                                <PasswordInput
                                    id={field}
                                    value={password.data[field]}
                                    onChange={(e) => password.setData(field, e.target.value)}
                                    autoComplete={autoComplete}
                                    required
                                />
                                <FieldError message={password.errors[field]} />
                            </div>
                        ))}
                        <Button type="submit" disabled={password.processing} className="w-max">
                            Update password
                        </Button>
                        {password.recentlySuccessful && <p className="text-sm font-semibold text-success">Password updated.</p>}
                    </form>
                </Card>
            </div>
        </AccountShell>
    );
}
