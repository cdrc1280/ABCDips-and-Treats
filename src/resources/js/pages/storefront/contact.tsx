import { Head, useForm, usePage } from '@inertiajs/react';
import { Clock, Mail, MapPin, Phone, Send } from 'lucide-react';

import { PageHeader } from '@/components/storefront/page-header';
import { FormField } from '@/components/storefront/simple-form';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

export default function Contact({ store }: { store: { address: string; phone: string; email: string; hours: string } }) {
    const user = usePage().props.auth.user;
    const form = useForm({ name: user?.name ?? '', email: user?.email ?? '', phone: user?.phone ?? '', subject: '', message: '' });

    const info = [
        { icon: MapPin, label: 'Visit', value: store.address },
        { icon: Phone, label: 'Call or text', value: store.phone, href: store.phone ? `tel:${store.phone}` : undefined },
        { icon: Mail, label: 'Email', value: store.email, href: store.email ? `mailto:${store.email}` : undefined },
        { icon: Clock, label: 'Hours', value: store.hours },
    ].filter((item) => item.value);

    return (
        <>
            <Head title="Contact Us" />
            <div className="mx-auto max-w-6xl px-4 pb-20 sm:px-6 lg:px-8">
                <PageHeader title="Contact us" subtitle="Questions about an order, a custom cake or a big event? We reply within 24 hours." />
                <div className="grid gap-8 lg:grid-cols-5">
                    <ul className="space-y-3 lg:col-span-2">
                        {info.map((item) => (
                            <li key={item.label} className="flex gap-4 rounded-2xl border border-primary/20 bg-card p-5">
                                <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-highlight/20 text-primary">
                                    <item.icon className="size-5" />
                                </span>
                                <span>
                                    <span className="block text-xs font-bold tracking-wider text-muted-foreground uppercase">{item.label}</span>
                                    {item.href ? (
                                        <a href={item.href} className="text-sm font-semibold text-foreground hover:underline">
                                            {item.value}
                                        </a>
                                    ) : (
                                        <span className="text-sm font-semibold text-foreground">{item.value}</span>
                                    )}
                                </span>
                            </li>
                        ))}
                    </ul>
                    <form
                        className="space-y-4 rounded-2xl border border-primary/20 bg-card p-6 shadow-sm lg:col-span-3"
                        onSubmit={(e) => {
                            e.preventDefault();
                            form.post('/contact', { preserveScroll: true, onSuccess: () => form.reset('subject', 'message') });
                        }}
                    >
                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField id="name" label="Name" error={form.errors.name}>
                                <Input id="name" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} required />
                            </FormField>
                            <FormField id="email" label="Email" error={form.errors.email}>
                                <Input
                                    id="email"
                                    type="email"
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                    required
                                />
                            </FormField>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <FormField id="phone" label="Phone (optional)" error={form.errors.phone}>
                                <Input id="phone" type="tel" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} />
                            </FormField>
                            <FormField id="subject" label="Subject" error={form.errors.subject}>
                                <Input id="subject" value={form.data.subject} onChange={(e) => form.setData('subject', e.target.value)} required />
                            </FormField>
                        </div>
                        <FormField id="message" label="Message" error={form.errors.message}>
                            <Textarea
                                id="message"
                                className="min-h-36"
                                value={form.data.message}
                                onChange={(e) => form.setData('message', e.target.value)}
                                required
                                minLength={10}
                                maxLength={3000}
                            />
                        </FormField>
                        <Button type="submit" disabled={form.processing}>
                            <Send className="size-4" /> Send message
                        </Button>
                    </form>
                </div>
            </div>
        </>
    );
}
