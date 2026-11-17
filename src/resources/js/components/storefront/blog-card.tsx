import { Link } from '@inertiajs/react';
import { Clock } from 'lucide-react';

import { formatDate } from '@/lib/format';
import type { BlogPostCard } from '@/types';

export function BlogCard({ post }: { post: BlogPostCard }) {
    return (
        <Link
            href={`/blog/${post.slug}`}
            className="group flex flex-col overflow-hidden rounded-2xl border border-primary/20 bg-card shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg"
        >
            <div className="aspect-video overflow-hidden bg-background">
                <img
                    src={post.cover_url ?? '/images/blog-banana-bread.jpg'}
                    alt=""
                    loading="lazy"
                    className="size-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
            </div>
            <div className="flex flex-1 flex-col p-5">
                {post.category && <span className="text-xs font-bold tracking-wider text-primary uppercase">{post.category}</span>}
                <h3 className="mt-1 line-clamp-2 text-lg font-semibold text-foreground group-hover:text-primary">{post.title}</h3>
                {post.excerpt && <p className="mt-2 line-clamp-3 text-sm text-muted-foreground">{post.excerpt}</p>}
                <p className="mt-auto flex items-center gap-2 pt-4 text-xs text-muted-foreground">
                    {formatDate(post.published_at)} · <Clock className="size-3" /> {post.reading_minutes} min read
                </p>
            </div>
        </Link>
    );
}
