import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, Check, Gift, ShoppingBag, Sparkles } from 'lucide-react';

import { CategoryIcon } from '@/components/storefront/category-icon';
import { ProductGrid } from '@/components/storefront/product-card';
import { Reveal, SectionHeading } from '@/components/storefront/reveal';
import { StoreReviews } from '@/components/storefront/reviews';
import { register, shop } from '@/routes';
import type { ProductCardData, StoreReviewsData, StorefrontCategory } from '@/types';

/** Real photo of our boxed banana bread; the hero_card_image setting overrides it. */
const HERO_PHOTO = '/images/textures/authentic_banana_bread_box.jpg';

type HomeContent = Record<string, string | null>;

interface Props {
    content: HomeContent;
    featured: ProductCardData[];
    bestSellers: ProductCardData[];
    newArrivals: ProductCardData[];
    categories: StorefrontCategory[];
    storeReviews?: StoreReviewsData;
}

const ctaPrimary =
    'flex items-center gap-2 rounded-2xl bg-highlight px-8 py-4 text-sm font-semibold text-highlight-foreground shadow-lg transition-all duration-300 hover:scale-[1.03]';

function SeeAll({ href, label }: { href: string; label: string }) {
    return (
        <Link href={href} className="group flex items-center gap-1.5 text-xs font-bold text-primary hover:underline sm:text-sm">
            {label} <ArrowRight className="size-4 transition-transform group-hover:translate-x-1" />
        </Link>
    );
}

export default function Home({ content, featured, bestSellers, categories, storeReviews }: Props) {
    const user = usePage().props.auth.user;

    return (
        <>
            <Head title="Banana bread & treats">
                <meta name="description" content={content.hero_description ?? ''} />
            </Head>
            <div className="space-y-16 overflow-hidden pb-20 md:space-y-24">
                <section className="mx-auto max-w-7xl px-4 pt-4 sm:px-6 sm:pt-8 lg:px-8">
                    <div className="grid grid-cols-[minmax(0,1fr)] items-center gap-6 lg:grid-cols-12 lg:gap-14">
                        <figure className="relative lg:order-2 lg:col-span-6">
                            <img
                                src={content.hero_card_image || HERO_PHOTO}
                                alt={content.hero_card_title ?? 'Banana bread slices packed in boxes'}
                                width={550}
                                height={740}
                                fetchPriority="high"
                                decoding="async"
                                className="aspect-4/3 w-full rounded-3xl object-cover shadow-lg sm:aspect-16/10 lg:aspect-4/5"
                            />
                            {content.hero_badge && (
                                <span className="absolute top-3 right-3 -rotate-3 rounded-full border-2 border-dashed border-primary/50 bg-card/95 px-3 py-1 text-[11px] font-semibold text-primary shadow-sm sm:top-5 sm:right-5 sm:text-xs">
                                    {content.hero_badge}
                                </span>
                            )}
                            {content.hero_card_title && (
                                <figcaption className="absolute bottom-3 left-3 max-w-[75%] rounded-l-md rounded-r-xl bg-kraft py-2.5 pr-4 pl-7 text-kraft-foreground shadow-md sm:bottom-5 sm:left-5">
                                    <span aria-hidden className="absolute top-1/2 left-2.5 size-2 -translate-y-1/2 rounded-full bg-background" />
                                    <span className="block font-display text-base leading-tight sm:text-lg">{content.hero_card_title}</span>
                                    {content.hero_card_subtitle && (
                                        <span className="block font-mono text-xs font-semibold tabular-nums sm:text-sm">
                                            {content.hero_card_subtitle}
                                        </span>
                                    )}
                                </figcaption>
                            )}
                        </figure>

                        <div className="space-y-5 lg:order-1 lg:col-span-6">
                            <h1 className="font-display text-4xl leading-[1.05] text-foreground sm:text-5xl lg:text-6xl">
                                {content.hero_title}
                                {content.hero_subtitle && <span className="block text-primary">{content.hero_subtitle}</span>}
                            </h1>
                            <p className="max-w-lg text-base leading-relaxed whitespace-pre-line text-muted-foreground sm:text-lg">
                                {content.hero_description}
                            </p>
                            <div className="flex flex-col gap-3 sm:flex-row">
                                <Link
                                    href={content.hero_btn_primary_url || shop().url}
                                    className="inline-flex h-12 items-center justify-center gap-2 rounded-xl bg-primary px-6 text-sm font-semibold text-primary-foreground shadow-sm transition-colors hover:bg-primary/90"
                                >
                                    {content.hero_btn_primary_text || 'See the menu'} <ArrowRight className="size-4" />
                                </Link>
                                {content.hero_btn_secondary_text && (
                                    <Link
                                        href={content.hero_btn_secondary_url || shop().url}
                                        className="inline-flex h-12 items-center justify-center rounded-xl border border-border bg-card px-6 text-sm font-semibold text-foreground transition-colors hover:border-primary/40 hover:bg-accent"
                                    >
                                        {content.hero_btn_secondary_text}
                                    </Link>
                                )}
                            </div>
                            <ul className="flex flex-wrap gap-x-5 gap-y-2 pt-1 text-sm text-muted-foreground">
                                {[content.hero_bullet_1, content.hero_bullet_2].filter(Boolean).map((bullet) => (
                                    <li key={bullet} className="flex items-center gap-1.5">
                                        <Check className="size-4 text-success" /> {bullet}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                </section>

                {categories.length > 0 && (
                    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <SectionHeading title="The menu" action={<SeeAll href="/shop" label="See everything" />} />
                        <div className="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-4">
                            {categories.map((category, i) => {
                                return (
                                    <Reveal key={category.slug} delay={i * 60}>
                                        <Link
                                            href={`/category/${category.slug}`}
                                            className="group flex h-full flex-col items-center justify-between rounded-3xl border border-border bg-card p-5 text-center shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                                        >
                                            <div className="mb-3.5 flex size-14 items-center justify-center overflow-hidden rounded-2xl border border-border bg-highlight/15 text-primary">
                                                {category.image_url ? (
                                                    <img src={category.image_url} alt="" className="size-full object-cover" />
                                                ) : (
                                                    <CategoryIcon slug={category.slug} className="size-7 text-primary" />
                                                )}
                                            </div>
                                            <h3 className="line-clamp-2 flex min-h-10 items-center text-sm leading-snug font-semibold text-foreground sm:text-base">
                                                {category.name}
                                            </h3>
                                            <span className="mt-2 rounded-full border border-border bg-muted px-2.5 py-0.5 text-[11px] font-bold text-muted-foreground">
                                                {category.products_count} items
                                            </span>
                                        </Link>
                                    </Reveal>
                                );
                            })}
                        </div>
                    </section>
                )}

                {featured.length > 0 && (
                    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <SectionHeading title="Baker's picks" action={<SeeAll href="/featured" label="All picks" />} />
                        <Reveal>
                            <ProductGrid products={featured.slice(0, 8)} />
                        </Reveal>
                    </section>
                )}

                {!user && (
                    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <Reveal className="flex flex-col items-start justify-between gap-4 rounded-3xl border border-primary/25 bg-highlight/15 p-6 sm:flex-row sm:items-center sm:p-8">
                            <div className="flex items-start gap-4">
                                <div className="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-primary text-primary-foreground">
                                    <Gift className="size-6" />
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-foreground">Create a free account</h3>
                                    <p className="text-sm text-muted-foreground">
                                        Track your orders, keep a wishlist, and check out faster next time.
                                    </p>
                                </div>
                            </div>
                            <Link
                                href={register().url}
                                className="rounded-xl bg-primary px-5 py-3 text-sm font-bold whitespace-nowrap text-primary-foreground hover:bg-primary/90"
                            >
                                Sign up, it's free
                            </Link>
                        </Reveal>
                    </section>
                )}

                <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <Reveal className="relative overflow-hidden rounded-3xl border border-sidebar-border bg-sidebar p-8 text-sidebar-foreground shadow-xl md:p-14">
                        <div className="grid grid-cols-1 items-center gap-8 md:grid-cols-12 lg:gap-12">
                            <div className="space-y-5 md:col-span-7">
                                <span className="inline-flex items-center gap-2 rounded-full border border-sidebar-border bg-highlight/15 px-3.5 py-1 text-xs font-bold text-sidebar-primary">
                                    <Sparkles className="size-3.5" /> {content.spotlight_tagline}
                                </span>
                                <h2 className="font-display text-3xl leading-tight md:text-4xl lg:text-5xl">{content.spotlight_title}</h2>
                                <p className="max-w-xl text-sm leading-relaxed whitespace-pre-line text-sidebar-foreground/80 md:text-base">
                                    {content.spotlight_description}
                                </p>
                                <Link href={content.spotlight_btn_url || '/shop'} className={`${ctaPrimary} inline-flex`}>
                                    <ShoppingBag className="size-4" /> {content.spotlight_btn_text}
                                </Link>
                            </div>
                            <div className="flex justify-center md:col-span-5">
                                <div className="group size-64 overflow-hidden rounded-3xl border border-sidebar-border bg-sidebar-accent p-3 shadow-xl md:size-80">
                                    <img
                                        src={content.spotlight_image || '/images/blog-custom-cake.jpg'}
                                        alt={content.spotlight_title ?? ''}
                                        loading="lazy"
                                        className="size-full rounded-2xl object-cover transition-transform duration-700 group-hover:scale-105"
                                    />
                                </div>
                            </div>
                        </div>
                    </Reveal>
                </section>

                {bestSellers.length > 0 && (
                    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <SectionHeading title="Best sellers" action={<SeeAll href="/best-sellers" label="All best sellers" />} />
                        <Reveal>
                            <ProductGrid products={bestSellers.slice(0, 8)} />
                        </Reveal>
                    </section>
                )}

                <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <StoreReviews data={storeReviews} />
                </section>
            </div>
        </>
    );
}
