import { Deferred, Head, Link } from '@inertiajs/react';
import { Coffee, Heart, Home, Sparkles, Star, type LucideIcon } from 'lucide-react';

import { Reveal, SectionHeading } from '@/components/storefront/reveal';
import { formatNumber } from '@/lib/format';

interface Item {
    icon?: string;
    title: string;
    desc: string;
    year?: string;
}

interface Props {
    content: Record<string, string> & { timeline: Item[]; values: Item[] };
    stats?: { happy_customers: number; signature_recipes: number; average_rating: number | null };
}

/** Real photo of our boxed loaves. */
const ABOUT_PHOTO = '/images/textures/real_loaf_vertical.jpg';

const ICONS: Record<string, LucideIcon> = { home: Home, heart: Heart, sparkles: Sparkles, coffee: Coffee, star: Star };

export default function About({ content, stats }: Props) {
    return (
        <>
            <Head title="About us" />
            <div className="space-y-20 pb-20">
                <section className="mx-auto grid max-w-6xl grid-cols-[minmax(0,1fr)] items-center gap-8 px-4 pt-8 sm:px-6 sm:pt-12 md:grid-cols-12 lg:px-8">
                    <div className="md:col-span-7">
                        <h1 className="font-display text-4xl leading-[1.08] whitespace-pre-line text-foreground sm:text-5xl">{content.hero_title}</h1>
                        <p className="mt-5 max-w-xl text-base leading-relaxed text-muted-foreground sm:text-lg">{content.hero_subtitle}</p>
                    </div>
                    <img
                        src={ABOUT_PHOTO}
                        alt="Banana bread slices packed in paper-lined boxes"
                        width={400}
                        height={620}
                        className="aspect-4/3 w-full rounded-3xl object-cover shadow-lg md:col-span-5 md:aspect-4/5"
                    />
                </section>

                <section className="mx-auto max-w-4xl px-4">
                    <SectionHeading title={content.timeline_title} />
                    <ol className="relative space-y-8 border-l-2 border-primary/30 pl-8">
                        {content.timeline.map((item, i) => {
                            const Icon = ICONS[item.icon ?? ''] ?? Sparkles;
                            return (
                                <Reveal key={`${item.year}-${i}`} delay={i * 80}>
                                    <li className="relative">
                                        <span className="absolute top-0 -left-[49px] flex size-8 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                            <Icon className="size-4" />
                                        </span>
                                        <span className="font-mono text-sm font-bold text-primary">{item.year}</span>
                                        <h3 className="text-lg font-semibold text-foreground">{item.title}</h3>
                                        <p className="mt-1 text-sm leading-relaxed text-muted-foreground">{item.desc}</p>
                                    </li>
                                </Reveal>
                            );
                        })}
                    </ol>
                </section>

                <section className="mx-auto max-w-6xl px-4">
                    <SectionHeading title={content.values_title} />
                    <div className="grid gap-6 md:grid-cols-3">
                        {content.values.map((value, i) => {
                            const Icon = ICONS[value.icon ?? ''] ?? Heart;
                            return (
                                <Reveal key={value.title} delay={i * 80} className="rounded-3xl border border-primary/20 bg-card p-6 shadow-sm">
                                    <div className="mb-4 flex size-12 items-center justify-center rounded-2xl bg-highlight/20 text-primary">
                                        <Icon className="size-6" />
                                    </div>
                                    <h3 className="text-lg font-semibold text-foreground">{value.title}</h3>
                                    <p className="mt-2 text-sm leading-relaxed text-muted-foreground">{value.desc}</p>
                                </Reveal>
                            );
                        })}
                    </div>
                </section>

                <section className="mx-auto max-w-5xl px-4">
                    <Deferred data="stats" fallback={<div className="h-32 animate-pulse rounded-3xl bg-background" />}>
                        <div className="grid grid-cols-3 gap-4 rounded-3xl bg-primary p-8 text-center text-sidebar-foreground">
                            {[
                                ['Happy customers', formatNumber(stats?.happy_customers)],
                                ['Signature recipes', formatNumber(stats?.signature_recipes)],
                                ['Average rating', stats?.average_rating ? `${stats.average_rating} ★` : 'New!'],
                            ].map(([label, value]) => (
                                <div key={label}>
                                    <p className="font-mono text-3xl font-black sm:text-4xl">{value}</p>
                                    <p className="mt-1 text-xs text-sidebar-primary sm:text-sm">{label}</p>
                                </div>
                            ))}
                        </div>
                    </Deferred>
                </section>

                <section className="mx-auto max-w-4xl px-4">
                    <div className="flex flex-col items-start justify-between gap-5 rounded-3xl border border-border bg-card p-6 sm:flex-row sm:items-center sm:p-8">
                        <div>
                            <h2 className="font-display text-2xl text-foreground sm:text-3xl">{content.cta_title}</h2>
                            <p className="mt-1 text-muted-foreground">{content.cta_subtitle}</p>
                        </div>
                        <Link
                            href={content.cta_button_url || '/shop'}
                            className="inline-flex h-12 shrink-0 items-center rounded-xl bg-primary px-6 text-sm font-semibold text-primary-foreground hover:bg-primary/90"
                        >
                            {content.cta_button_text}
                        </Link>
                    </div>
                </section>
            </div>
        </>
    );
}
