import { Head, Link } from '@inertiajs/react';
import { ChevronDown, Search } from 'lucide-react';
import { useState } from 'react';

import { EmptyState, PageHeader } from '@/components/storefront/page-header';
import { cn } from '@/lib/utils';

interface Faq {
    id: string;
    question: string;
    answer: string;
    category: string;
}

export default function FaqPage({ faqs }: { faqs: Faq[] }) {
    const [query, setQuery] = useState('');
    const [open, setOpen] = useState<string | null>(faqs[0]?.id ?? null);
    const term = query.trim().toLowerCase();
    const visible = term ? faqs.filter((f) => `${f.question} ${f.answer}`.toLowerCase().includes(term)) : faqs;
    const groups = visible.reduce<Record<string, Faq[]>>((acc, faq) => ({ ...acc, [faq.category]: [...(acc[faq.category] ?? []), faq] }), {});

    return (
        <>
            <Head title="FAQ" />
            <div className="mx-auto max-w-3xl px-4 pb-20">
                <PageHeader title="Common questions" subtitle="Ordering, delivery, payment and our treats." />
                <label className="relative mb-8 block">
                    <span className="sr-only">Search questions</span>
                    <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        placeholder="Search questions…"
                        className="w-full rounded-xl border border-primary/30 bg-card py-3 pr-4 pl-9 text-sm text-foreground focus:ring-2 focus:ring-primary focus:outline-none"
                    />
                </label>
                {visible.length === 0 ? (
                    <EmptyState
                        title="No matching questions"
                        description="Try another word, or ask us directly."
                        action={
                            <Link href="/contact" className="font-bold text-primary hover:underline">
                                Contact us →
                            </Link>
                        }
                    />
                ) : (
                    Object.entries(groups).map(([category, items]) => (
                        <section key={category} className="mb-8">
                            <h2 className="mb-3 text-xs font-bold tracking-widest text-primary uppercase">{category}</h2>
                            <div className="space-y-2">
                                {items.map((faq) => {
                                    const expanded = open === faq.id;
                                    return (
                                        <div key={faq.id} className="overflow-hidden rounded-2xl border border-primary/20 bg-card">
                                            <button
                                                type="button"
                                                aria-expanded={expanded}
                                                onClick={() => setOpen(expanded ? null : faq.id)}
                                                className="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-sm font-bold text-foreground"
                                            >
                                                {faq.question}
                                                <ChevronDown
                                                    className={cn('size-4 shrink-0 text-primary transition-transform', expanded && 'rotate-180')}
                                                />
                                            </button>
                                            {expanded && (
                                                <p className="animate-in px-5 pb-5 text-sm leading-relaxed whitespace-pre-line text-muted-foreground fade-in">
                                                    {faq.answer}
                                                </p>
                                            )}
                                        </div>
                                    );
                                })}
                            </div>
                        </section>
                    ))
                )}
            </div>
        </>
    );
}
