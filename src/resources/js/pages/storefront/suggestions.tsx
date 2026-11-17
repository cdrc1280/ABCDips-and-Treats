import { Head, useForm, usePage } from '@inertiajs/react';
import { Lightbulb, MessageSquareHeart, Sparkles, Wrench } from 'lucide-react';

import { PageHeader } from '@/components/storefront/page-header';
import { FormField } from '@/components/storefront/simple-form';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';

const CATEGORIES = [
    { value: 'product_idea', label: 'Product idea', icon: Lightbulb },
    { value: 'service_feedback', label: 'Service feedback', icon: MessageSquareHeart },
    { value: 'feature_request', label: 'Website feature', icon: Wrench },
    { value: 'other', label: 'Something else', icon: Sparkles },
];

export default function Suggestions() {
    const user = usePage().props.auth.user;
    const form = useForm({ name: user?.name ?? '', email: user?.email ?? '', category: 'product_idea', subject: '', message: '' });

    return (
        <>
            <Head title="Suggest an Idea" />
            <div className="mx-auto max-w-2xl px-4 pb-20">
                <PageHeader title="Suggest something" subtitle="A flavour you miss, a better way to order: tell us. We read every one." />
                <form
                    className="space-y-5 rounded-2xl border border-primary/20 bg-card p-6 shadow-sm"
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post('/suggestions', { preserveScroll: true, onSuccess: () => form.reset('subject', 'message') });
                    }}
                >
                    <div className="grid grid-cols-2 gap-2 sm:grid-cols-4" role="radiogroup" aria-label="Category">
                        {CATEGORIES.map((category) => (
                            <button
                                key={category.value}
                                type="button"
                                role="radio"
                                aria-checked={form.data.category === category.value}
                                onClick={() => form.setData('category', category.value)}
                                className={cn(
                                    'flex flex-col items-center gap-1.5 rounded-xl border-2 p-3 text-xs font-bold transition-all',
                                    form.data.category === category.value
                                        ? 'border-primary bg-highlight/15 text-primary'
                                        : 'border-primary/20 text-muted-foreground',
                                )}
                            >
                                <category.icon className="size-5" />
                                {category.label}
                            </button>
                        ))}
                    </div>
                    <div className="grid gap-4 sm:grid-cols-2">
                        <FormField id="name" label="Name" error={form.errors.name}>
                            <Input id="name" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} required />
                        </FormField>
                        <FormField id="email" label="Email" error={form.errors.email}>
                            <Input id="email" type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} required />
                        </FormField>
                    </div>
                    <FormField id="subject" label="In a few words" error={form.errors.subject}>
                        <Input id="subject" value={form.data.subject} onChange={(e) => form.setData('subject', e.target.value)} required />
                    </FormField>
                    <FormField id="message" label="Tell us more" error={form.errors.message}>
                        <Textarea
                            id="message"
                            className="min-h-32"
                            value={form.data.message}
                            onChange={(e) => form.setData('message', e.target.value)}
                            required
                            minLength={10}
                            maxLength={2000}
                        />
                    </FormField>
                    <Button type="submit" disabled={form.processing}>
                        Send suggestion
                    </Button>
                </form>
            </div>
        </>
    );
}
