import { Link, router, useForm, usePage } from '@inertiajs/react';
import { BadgeCheck, ImagePlus, Star, ThumbsUp, X } from 'lucide-react';
import { useState } from 'react';

import { Button } from '@/components/ui/button';
import { formatDate } from '@/lib/format';
import { cn } from '@/lib/utils';
import reviewRoutes from '@/routes/reviews';
import type { StoreReviewsData, StorefrontReview } from '@/types';

export function Stars({ value, className }: { value: number; className?: string }) {
    return (
        <span className={cn('flex items-center gap-0.5', className)} aria-label={`${value} out of 5 stars`}>
            {[1, 2, 3, 4, 5].map((n) => (
                <Star key={n} className={cn('size-4', n <= Math.round(value) ? 'fill-highlight text-highlight' : 'text-muted-foreground/30')} />
            ))}
        </span>
    );
}

export function ReviewCard({ review }: { review: StorefrontReview }) {
    return (
        <article className="rounded-2xl border border-primary/20 bg-card p-5 shadow-sm">
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="flex items-center gap-1.5 text-sm font-bold text-foreground">
                        {review.reviewer_name}
                        {review.is_verified_buyer && (
                            <span className="flex items-center gap-0.5 text-[10px] font-bold text-success uppercase">
                                <BadgeCheck className="size-3.5" /> Verified buyer
                            </span>
                        )}
                    </p>
                    <p className="text-xs text-muted-foreground">{formatDate(review.created_at)}</p>
                </div>
                <Stars value={review.rating} />
            </div>
            {review.title && <h4 className="mt-3 text-sm font-bold text-foreground">{review.title}</h4>}
            <p className="mt-1 text-sm leading-relaxed whitespace-pre-line text-muted-foreground">{review.comment}</p>
            {review.photos.length > 0 && (
                <div className="mt-3 flex gap-2">
                    {review.photos.map((src) => (
                        <a key={src} href={src} target="_blank" rel="noreferrer" className="size-16 overflow-hidden rounded-lg border">
                            <img src={src} alt="Customer photo" loading="lazy" className="size-full object-cover" />
                        </a>
                    ))}
                </div>
            )}
            <button
                type="button"
                onClick={() => router.post(reviewRoutes.helpful(review.id).url, {}, { preserveScroll: true, preserveState: true })}
                className="mt-3 flex items-center gap-1.5 text-xs font-semibold text-muted-foreground hover:text-primary"
            >
                <ThumbsUp className="size-3.5" /> Helpful ({review.helpful_votes})
            </button>
        </article>
    );
}

/** Review form for a product (productId) or for the bakery overall. Guests give a name and email. */
export function ReviewForm({ productId, onDone }: { productId?: string; onDone?: () => void }) {
    const user = usePage().props.auth.user;
    const form = useForm({
        product_id: productId ?? null,
        rating: 5,
        title: '',
        comment: '',
        is_anonymous: false,
        reviewer_name: '',
        reviewer_email: '',
        photos: [] as File[],
    });

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        form.post(reviewRoutes.store().url, {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                onDone?.();
            },
        });
    };

    const field =
        'border-primary/30 text-foreground focus:ring-primary w-full rounded-xl border bg-card px-3 py-2 text-sm focus:ring-2 focus:outline-none';

    return (
        <form onSubmit={submit} className="space-y-4 rounded-2xl border border-primary/20 bg-card p-5">
            <div>
                <span className="mb-1 block text-sm font-bold text-foreground">Your rating</span>
                <div className="flex gap-1" role="radiogroup" aria-label="Rating">
                    {[1, 2, 3, 4, 5].map((n) => (
                        <button
                            key={n}
                            type="button"
                            role="radio"
                            aria-checked={form.data.rating === n}
                            aria-label={`${n} stars`}
                            onClick={() => form.setData('rating', n)}
                        >
                            <Star
                                className={cn(
                                    'size-7 transition-transform hover:scale-110',
                                    n <= form.data.rating ? 'fill-highlight text-highlight' : 'text-muted-foreground/40',
                                )}
                            />
                        </button>
                    ))}
                </div>
            </div>
            {!user && (
                <div className="grid gap-3 sm:grid-cols-2">
                    <label className="space-y-1 text-sm">
                        <span className="font-semibold text-foreground">Name</span>
                        <input
                            className={field}
                            value={form.data.reviewer_name}
                            onChange={(e) => form.setData('reviewer_name', e.target.value)}
                            required
                        />
                        {form.errors.reviewer_name && <span className="text-xs text-destructive">{form.errors.reviewer_name}</span>}
                    </label>
                    <label className="space-y-1 text-sm">
                        <span className="font-semibold text-foreground">Email (not shown)</span>
                        <input
                            type="email"
                            className={field}
                            value={form.data.reviewer_email}
                            onChange={(e) => form.setData('reviewer_email', e.target.value)}
                            required
                        />
                        {form.errors.reviewer_email && <span className="text-xs text-destructive">{form.errors.reviewer_email}</span>}
                    </label>
                </div>
            )}
            <label className="block space-y-1 text-sm">
                <span className="font-semibold text-foreground">Title (optional)</span>
                <input className={field} value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} maxLength={255} />
            </label>
            <label className="block space-y-1 text-sm">
                <span className="font-semibold text-foreground">Your review</span>
                <textarea
                    className={cn(field, 'min-h-28')}
                    value={form.data.comment}
                    onChange={(e) => form.setData('comment', e.target.value)}
                    required
                    minLength={10}
                    maxLength={3000}
                />
                {form.errors.comment && <span className="text-xs text-destructive">{form.errors.comment}</span>}
            </label>
            <div className="flex flex-wrap items-center gap-3">
                <label className="flex cursor-pointer items-center gap-1.5 rounded-xl border border-primary/30 px-3 py-2 text-xs font-bold text-primary hover:bg-highlight/20">
                    <ImagePlus className="size-4" /> Add photos
                    <input
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                        className="sr-only"
                        onChange={(e) => form.setData('photos', [...form.data.photos, ...Array.from(e.target.files ?? [])].slice(0, 4))}
                    />
                </label>
                {form.data.photos.map((file, i) => (
                    <span key={`${file.name}-${i}`} className="flex items-center gap-1 rounded-lg bg-background px-2 py-1 text-xs">
                        {file.name}
                        <button
                            type="button"
                            aria-label={`Remove ${file.name}`}
                            onClick={() =>
                                form.setData(
                                    'photos',
                                    form.data.photos.filter((_, j) => j !== i),
                                )
                            }
                        >
                            <X className="size-3" />
                        </button>
                    </span>
                ))}
                {user && (
                    <label className="flex items-center gap-2 text-xs text-muted-foreground">
                        <input
                            type="checkbox"
                            checked={form.data.is_anonymous}
                            onChange={(e) => form.setData('is_anonymous', e.target.checked)}
                            className="accent-primary"
                        />
                        Post anonymously
                    </label>
                )}
            </div>
            {(form.errors.photos || form.errors['photos.0']) && (
                <p className="text-xs text-destructive">{form.errors.photos ?? form.errors['photos.0']}</p>
            )}
            <Button type="submit" disabled={form.processing}>
                Submit review
            </Button>
        </form>
    );
}

/** Store-wide rating summary, the latest reviews and a "write a review" form. */
export function StoreReviews({ data }: { data: StoreReviewsData | undefined }) {
    const [writing, setWriting] = useState(false);
    if (!data) return <div className="h-64 animate-pulse rounded-3xl bg-muted" />;
    const { stats } = data;
    const total = stats.total_reviews || 0;

    return (
        <div className="space-y-8">
            <div className="grid gap-8 rounded-3xl border border-sidebar-border bg-sidebar p-8 text-sidebar-foreground shadow-xl md:p-12 lg:grid-cols-3">
                <div className="space-y-2 text-center lg:text-left">
                    <div className="text-xs font-semibold tracking-[0.12em] text-sidebar-primary uppercase">bakery experience &amp; service</div>
                    {total > 0 ? (
                        <>
                            <div className="flex items-baseline justify-center gap-3 lg:justify-start">
                                <span className="text-6xl font-black tracking-tight">{stats.avg_rating}</span>
                                <span className="text-2xl text-sidebar-primary">/ 5.0</span>
                            </div>
                            <Stars value={stats.avg_rating ?? 0} className="justify-center lg:justify-start" />
                            <p className="text-xs text-sidebar-primary">Based on {total} customer reviews</p>
                        </>
                    ) : (
                        <p className="text-lg font-bold">Be the first to review us!</p>
                    )}
                </div>
                <div className="space-y-2 rounded-2xl border border-sidebar-border bg-sidebar-accent p-6">
                    {(['5', '4', '3', '2', '1'] as const).map((n) => {
                        const count = stats.distribution[n] ?? 0;
                        return (
                            <div key={n} className="flex items-center gap-3 text-xs">
                                <span className="w-8 font-bold">{n} ★</span>
                                <div className="h-2 flex-1 overflow-hidden rounded-full bg-sidebar-border">
                                    <div
                                        className="h-full rounded-full bg-sidebar-primary"
                                        style={{ width: total ? `${(count / total) * 100}%` : '0%' }}
                                    />
                                </div>
                                <span className="w-8 text-right font-mono">{count}</span>
                            </div>
                        );
                    })}
                </div>
                <div className="flex flex-col items-center justify-center gap-3 text-center">
                    <p className="text-sm text-sidebar-foreground/80">Tried our treats? Tell other customers what you loved.</p>
                    <Button variant="secondary" onClick={() => setWriting((w) => !w)}>
                        {writing ? 'Close' : 'Write a review'}
                    </Button>
                    <Link href="/shop" className="text-xs font-bold text-sidebar-primary hover:underline">
                        Browse the menu →
                    </Link>
                </div>
            </div>
            {writing && <ReviewForm onDone={() => setWriting(false)} />}
            {data.reviews.data.length > 0 && (
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {data.reviews.data.slice(0, 6).map((review) => (
                        <ReviewCard key={review.id} review={review} />
                    ))}
                </div>
            )}
        </div>
    );
}
