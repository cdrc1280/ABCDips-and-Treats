import { Head, router, usePage } from '@inertiajs/react';
import { Bot, Info, Loader2, Send, Sparkles, User } from 'lucide-react';
import { useState, type FormEvent } from 'react';

import { InputError } from '@/components/custom/field';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { dashboard } from '@/routes/admin';
import { ask } from '@/routes/admin/ai-advisor';
import type { AdvisorAnswer } from '@/types';

const topics = [
    { value: 'general', label: 'General' },
    { value: 'sales', label: 'Sales' },
    { value: 'inventory', label: 'Inventory' },
    { value: 'products', label: 'Products' },
    { value: 'orders', label: 'Orders' },
    { value: 'customers', label: 'Customers' },
];

const suggestions = [
    'Which ingredients should I reorder this week?',
    'What were my best-selling products this month?',
    'Suggest a weekend promo to move slow sellers.',
    'How can I reduce packaging costs per order?',
];

export default function AiAdvisor({ configured }: { configured: boolean }) {
    const flash = usePage().flash as { advisor?: AdvisorAnswer };
    const [conversation, setConversation] = useState<AdvisorAnswer[]>([]);
    const [prompt, setPrompt] = useState('');
    const [category, setCategory] = useState('general');
    const [pending, setPending] = useState<string | null>(null);
    const [error, setError] = useState<string | undefined>();

    // Append each flashed answer once.
    if (flash.advisor && !conversation.some((entry) => entry.id === flash.advisor!.id)) {
        setConversation([...conversation, flash.advisor]);
        setPending(null);
    }

    const send = (question: string) => {
        if (question.trim().length < 2) return;
        router.post(
            ask().url,
            { prompt: question, category },
            {
                preserveScroll: true,
                preserveState: true,
                onStart: () => {
                    setPending(question);
                    setError(undefined);
                },
                onError: (errors) => {
                    setError(errors.prompt);
                    setPending(null);
                },
                onSuccess: () => setPrompt(''),
                onFinish: () => setPending(null),
            },
        );
    };

    const submit = (event: FormEvent) => {
        event.preventDefault();
        send(prompt);
    };

    return (
        <>
            <Head title="AI advisor" />
            <div className="mx-auto grid max-w-3xl gap-4">
                <header className="flex items-center gap-3">
                    <span className="flex size-10 items-center justify-center rounded-md bg-primary/10 text-primary">
                        <Sparkles className="size-5" />
                    </span>
                    <div>
                        <h1 className="font-display text-2xl leading-tight tracking-tight md:text-[1.75rem]">AI bakery advisor</h1>
                        <p className="text-sm text-muted-foreground">Answers use your live sales, menu and stock figures.</p>
                    </div>
                </header>

                {!configured && (
                    <Alert>
                        <Info />
                        <AlertTitle>Running in basic mode</AlertTitle>
                        <AlertDescription>
                            No AI key is set, so answers come from built-in rules. Add GEMINI_API_KEY or OPENAI_API_KEY to the server environment for
                            full answers.
                        </AlertDescription>
                    </Alert>
                )}

                <Card>
                    <CardContent className="grid gap-4">
                        {conversation.length === 0 && !pending && (
                            <div className="grid gap-2">
                                <p className="text-sm text-muted-foreground">Try one of these:</p>
                                <div className="flex flex-wrap gap-2">
                                    {suggestions.map((s) => (
                                        <Button key={s} variant="outline" size="sm" onClick={() => send(s)}>
                                            {s}
                                        </Button>
                                    ))}
                                </div>
                            </div>
                        )}

                        <div className="grid gap-4">
                            {conversation.map((entry) => (
                                <div key={entry.id} className="grid gap-3">
                                    <div className="flex justify-end">
                                        <div className="max-w-[85%] rounded-2xl bg-primary px-4 py-2.5 text-sm text-primary-foreground">
                                            <div className="mb-1 flex items-center gap-1.5 text-xs opacity-80">
                                                <User className="size-3.5" /> You
                                            </div>
                                            {entry.question}
                                        </div>
                                    </div>
                                    <div className="flex">
                                        <div className="max-w-[85%] rounded-2xl bg-muted px-4 py-2.5 text-sm">
                                            <div className="mb-1 flex items-center gap-1.5 text-xs text-muted-foreground">
                                                <Bot className="size-3.5" /> {entry.source || 'Advisor'}
                                            </div>
                                            <p className="whitespace-pre-wrap">{entry.answer}</p>
                                        </div>
                                    </div>
                                </div>
                            ))}
                            {pending && (
                                <div className="grid gap-3">
                                    <div className="flex justify-end">
                                        <div className="max-w-[85%] rounded-2xl bg-primary/80 px-4 py-2.5 text-sm text-primary-foreground">
                                            {pending}
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                        <Loader2 className="size-4 animate-spin" /> Thinking…
                                    </div>
                                </div>
                            )}
                        </div>

                        <form onSubmit={submit} className="grid gap-2 border-t pt-4">
                            <ToggleGroup
                                type="single"
                                variant="outline"
                                size="sm"
                                value={category}
                                onValueChange={(v) => v && setCategory(v)}
                                className="flex-wrap justify-start"
                            >
                                {topics.map((t) => (
                                    <ToggleGroupItem key={t.value} value={t.value}>
                                        {t.label}
                                    </ToggleGroupItem>
                                ))}
                            </ToggleGroup>
                            <Textarea
                                rows={3}
                                value={prompt}
                                onChange={(event) => setPrompt(event.target.value)}
                                onKeyDown={(event) => {
                                    if (event.key === 'Enter' && !event.shiftKey) {
                                        event.preventDefault();
                                        send(prompt);
                                    }
                                }}
                                placeholder="Ask about sales, stock, pricing or promos…"
                                aria-invalid={!!error}
                            />
                            <InputError message={error} />
                            <div className="flex justify-end">
                                <Button type="submit" disabled={pending !== null || prompt.trim().length < 2}>
                                    <Send /> Ask
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AiAdvisor.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'AI advisor' }] };
