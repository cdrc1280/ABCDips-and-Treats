import { Deferred, Head, Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { useState } from 'react';

import { ProductGrid } from '@/components/storefront/product-card';
import { ProductGallery, ProductPurchasePanel } from '@/components/storefront/product-purchase-panel';
import { TurnTheLoaf } from '@/components/storefront/turn-the-loaf';
import { ReviewCard, ReviewForm } from '@/components/storefront/reviews';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { ProductCardData, ProductDetailData, StorefrontReview } from '@/types';

interface Props {
    product: ProductDetailData;
    related?: ProductCardData[];
    reviews?: StorefrontReview[];
}

export default function Product({ product, related, reviews }: Props) {
    const [writing, setWriting] = useState(false);
    const n = product.nutrition;

    return (
        <>
            <Head title={product.seo.title}>
                <meta name="description" content={product.seo.description ?? ''} />
                {product.image_url && <meta property="og:image" content={product.image_url} />}
            </Head>
            <div className="mx-auto max-w-7xl space-y-16 px-4 pb-20 sm:px-6 lg:px-8">
                <nav aria-label="Breadcrumb" className="flex flex-wrap items-center gap-1 pt-6 text-xs font-semibold text-muted-foreground">
                    <Link href="/" className="hover:text-primary">
                        Home
                    </Link>
                    <ChevronRight className="size-3" />
                    <Link href="/shop" className="hover:text-primary">
                        Menu
                    </Link>
                    {product.category && (
                        <>
                            <ChevronRight className="size-3" />
                            <Link href={`/category/${product.category.slug}`} className="hover:text-primary">
                                {product.category.name}
                            </Link>
                        </>
                    )}
                    <ChevronRight className="size-3" />
                    <span className="truncate text-primary">{product.name}</span>
                </nav>

                <div className="grid gap-8 rounded-3xl border border-primary/20 bg-card p-4 shadow-sm sm:p-8 md:grid-cols-12">
                    <div className="space-y-3 md:col-span-5">
                        <ProductGallery product={product} />
                        {product.has_3d && <TurnTheLoaf productName={product.name} />}
                    </div>
                    <div className="md:col-span-7">
                        <ProductPurchasePanel product={product} stickyBar />
                    </div>
                </div>

                <Tabs defaultValue="description" className="space-y-4">
                    <TabsList>
                        <TabsTrigger value="description">Description</TabsTrigger>
                        <TabsTrigger value="allergens">Allergens</TabsTrigger>
                        {n && <TabsTrigger value="nutrition">Nutrition</TabsTrigger>}
                        <TabsTrigger value="reviews">Reviews ({product.reviews_count})</TabsTrigger>
                    </TabsList>
                    <TabsContent value="description" className="max-w-3xl text-sm leading-relaxed whitespace-pre-line text-muted-foreground">
                        {product.description || product.short_description || 'Freshly baked in small batches.'}
                        {product.tags.length > 0 && (
                            <div className="mt-4 flex flex-wrap gap-2">
                                {product.tags.map((tag) => (
                                    <span key={tag} className="rounded-full bg-highlight/20 px-3 py-1 text-xs font-semibold text-primary">
                                        #{tag}
                                    </span>
                                ))}
                            </div>
                        )}
                    </TabsContent>
                    <TabsContent value="allergens">
                        {product.allergens.length ? (
                            <ul className="flex flex-wrap gap-2">
                                {product.allergens.map((a) => (
                                    <li
                                        key={a.name}
                                        className="rounded-full border border-warning/30 bg-warning/10 px-3 py-1 text-xs font-bold text-warning-ink"
                                    >
                                        {a.name}
                                        {a.type ? ` · ${a.type}` : ''}
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="text-sm text-muted-foreground">No allergens listed. Tell us about dietary needs in your order notes.</p>
                        )}
                    </TabsContent>
                    {n && (
                        <TabsContent value="nutrition">
                            <dl className="grid max-w-xl grid-cols-2 gap-x-8 gap-y-2 text-sm sm:grid-cols-3">
                                {[
                                    ['Serving', n.serving_size ?? '—'],
                                    ['Calories', n.calories ?? '—'],
                                    ['Fat', `${n.fat_g} g`],
                                    ['Carbs', `${n.carbs_g} g`],
                                    ['Protein', `${n.protein_g} g`],
                                    ['Sugar', `${n.sugar_g} g`],
                                    ['Sodium', `${n.sodium_mg} mg`],
                                ].map(([label, value]) => (
                                    <div key={label as string}>
                                        <dt className="text-xs text-muted-foreground">{label}</dt>
                                        <dd className="font-mono font-bold text-foreground">{value}</dd>
                                    </div>
                                ))}
                            </dl>
                        </TabsContent>
                    )}
                    <TabsContent value="reviews" className="space-y-4">
                        <Button variant="outline" onClick={() => setWriting((w) => !w)}>
                            {writing ? 'Cancel' : 'Write a review'}
                        </Button>
                        {writing && <ReviewForm productId={product.id} onDone={() => setWriting(false)} />}
                        <Deferred data="reviews" fallback={<div className="h-32 animate-pulse rounded-2xl bg-background" />}>
                            {reviews && reviews.length > 0 ? (
                                <div className="grid gap-4 md:grid-cols-2">
                                    {reviews.map((review) => (
                                        <ReviewCard key={review.id} review={review} />
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground">No reviews yet — be the first!</p>
                            )}
                        </Deferred>
                    </TabsContent>
                </Tabs>

                <Deferred data="related" fallback={<></>}>
                    {related && related.length > 0 ? (
                        <section>
                            <h2 className="mb-6 font-display text-2xl text-foreground">You may also like</h2>
                            <ProductGrid products={related} />
                        </section>
                    ) : (
                        <></>
                    )}
                </Deferred>
            </div>
        </>
    );
}
