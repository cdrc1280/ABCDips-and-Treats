import { router, usePage } from '@inertiajs/react';
import { Heart } from 'lucide-react';

import { cn } from '@/lib/utils';
import { login } from '@/routes';
import wishlist from '@/routes/wishlist';

/** Heart toggle; guests are sent to sign in. */
export function WishlistButton({ productId, className, withLabel = false }: { productId: string; className?: string; withLabel?: boolean }) {
    const { auth, wishlist_ids } = usePage().props;
    const saved = wishlist_ids.includes(productId);

    const toggle = (event: React.MouseEvent) => {
        event.preventDefault();
        event.stopPropagation();
        if (!auth.user) {
            router.visit(login().url);
            return;
        }
        router.post(wishlist.toggle(productId).url, {}, { preserveScroll: true, preserveState: true });
    };

    return (
        <button
            type="button"
            onClick={toggle}
            aria-pressed={saved}
            aria-label={saved ? 'Remove from wishlist' : 'Save to wishlist'}
            title={saved ? 'Remove from wishlist' : 'Save to wishlist'}
            className={cn(
                'flex items-center justify-center gap-1.5 transition-all duration-300 active:scale-90',
                saved ? 'text-destructive-ink' : 'text-muted-foreground hover:text-destructive-ink',
                className,
            )}
        >
            <Heart className="size-4" fill={saved ? 'currentColor' : 'none'} />
            {withLabel && <span className="text-xs font-semibold">{saved ? 'Saved' : 'Save to wishlist'}</span>}
        </button>
    );
}
