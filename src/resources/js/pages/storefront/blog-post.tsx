import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Clock } from 'lucide-react';

import { BlogCard } from '@/components/storefront/blog-card';
import { formatDate } from '@/lib/format';
import type { BlogPostCard } from '@/types';

interface Props {
    post: BlogPostCard & { body: string; tags: string[]; seo_title: string; seo_description: string | null };
    more: BlogPostCard[];
}

export default function BlogPost({ post, more }: Props) {
    return (
        <>
            <Head title={post.seo_title}>
                <meta name="description" content={post.seo_description ?? ''} />
                {post.cover_url && <meta property="og:image" content={post.cover_url} />}
            </Head>
            <article className="mx-auto max-w-3xl px-4 pt-8 pb-16">
                <Link href="/blog" className="mb-6 inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:underline">
                    <ArrowLeft className="size-4" /> All posts
                </Link>
                {post.category && <p className="text-xs font-bold tracking-widest text-primary uppercase">{post.category}</p>}
                <h1 className="mt-2 text-3xl leading-tight font-black text-foreground sm:text-4xl">{post.title}</h1>
                <p className="mt-3 flex items-center gap-2 text-sm text-muted-foreground">
                    {post.author && <>{post.author} · </>}
                    {formatDate(post.published_at)} · <Clock className="size-3.5" /> {post.reading_minutes} min read
                </p>
                {post.cover_url && <img src={post.cover_url} alt="" className="mt-8 aspect-video w-full rounded-3xl object-cover shadow-lg" />}
                {/* Body is sanitised on the server (App\Domains\Storefront\Support\SafeHtml). */}
                <div
                    className="mt-10 space-y-4 leading-relaxed text-foreground [&_a]:text-primary [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:border-border [&_blockquote]:pl-4 [&_blockquote]:italic [&_h2]:mt-8 [&_h2]:font-display [&_h2]:text-2xl [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-bold [&_iframe]:aspect-video [&_iframe]:w-full [&_iframe]:rounded-2xl [&_img]:rounded-2xl [&_li]:ml-5 [&_ol]:list-decimal [&_ul]:list-disc"
                    dangerouslySetInnerHTML={{ __html: post.body }}
                />
                {post.tags.length > 0 && (
                    <div className="mt-10 flex flex-wrap gap-2">
                        {post.tags.map((tag) => (
                            <span key={tag} className="rounded-full bg-highlight/20 px-3 py-1 text-xs font-semibold text-primary">
                                #{tag}
                            </span>
                        ))}
                    </div>
                )}
            </article>
            {more.length > 0 && (
                <section className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                    <h2 className="mb-6 font-display text-2xl text-foreground">More from the kitchen</h2>
                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {more.map((p) => (
                            <BlogCard key={p.id} post={p} />
                        ))}
                    </div>
                </section>
            )}
        </>
    );
}
