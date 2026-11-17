import { Head, InfiniteScroll, Link } from '@inertiajs/react';
import { Newspaper } from 'lucide-react';

import { BlogCard } from '@/components/storefront/blog-card';
import { EmptyState, PageHeader } from '@/components/storefront/page-header';
import { cn } from '@/lib/utils';
import type { BlogPostCard } from '@/types';

interface Props {
    category: string | null;
    categories: string[];
    posts: { data: BlogPostCard[] };
}

export default function Blog({ category, categories, posts }: Props) {
    const pill = (active: boolean) =>
        cn(
            'rounded-full px-4 py-2 text-xs font-bold whitespace-nowrap transition-all',
            active ? 'bg-primary text-primary-foreground' : 'border border-primary/20 bg-background text-primary hover:bg-highlight/25',
        );

    return (
        <>
            <Head title="Blog & Vlog" />
            <div className="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
                <PageHeader title="Blog & vlog" subtitle="Recipes, behind-the-scenes and stories from our kitchen." />
                {categories.length > 0 && (
                    <nav className="no-scrollbar mb-8 flex justify-center gap-2 overflow-x-auto" aria-label="Blog categories">
                        <Link href="/blog" className={pill(!category)} preserveScroll>
                            All
                        </Link>
                        {categories.map((c) => (
                            <Link key={c} href={`/blog?category=${encodeURIComponent(c)}`} className={pill(category === c)} preserveScroll>
                                {c}
                            </Link>
                        ))}
                    </nav>
                )}
                {posts.data.length === 0 ? (
                    <EmptyState
                        icon={<Newspaper className="size-7" />}
                        title="No posts yet"
                        description="Fresh stories are in the oven — check back soon."
                    />
                ) : (
                    <InfiniteScroll data="posts" className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        {posts.data.map((post) => (
                            <BlogCard key={post.id} post={post} />
                        ))}
                    </InfiniteScroll>
                )}
            </div>
        </>
    );
}
