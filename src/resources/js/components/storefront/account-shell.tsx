import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Heart, Package, User } from 'lucide-react';
import type { ReactNode } from 'react';

import { cn } from '@/lib/utils';

const TABS = [
    { href: '/account/orders', label: 'My Orders', icon: Package },
    { href: '/account/wishlist', label: 'My Wishlist', icon: Heart },
    { href: '/account/profile', label: 'My Profile', icon: User },
];

/** Account tab bar + content container (rendered inside the storefront layout). */
export function AccountShell({ children }: { children: ReactNode }) {
    const url = usePage().url;

    return (
        <div className="mx-auto max-w-6xl px-4 py-8 sm:px-6 md:py-12 lg:px-8">
            <div className="mb-8 flex flex-col items-start justify-between gap-3 rounded-xl border bg-card p-3 sm:flex-row sm:items-center">
                <Link
                    href="/"
                    className="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                >
                    <ArrowLeft className="size-4" /> Back to Store
                </Link>
                <nav className="flex w-full items-center gap-1.5 overflow-x-auto sm:w-auto" aria-label="Account">
                    {TABS.map((tab) => {
                        const active = url.startsWith(tab.href);
                        return (
                            <Link
                                key={tab.href}
                                href={tab.href}
                                aria-current={active ? 'page' : undefined}
                                className={cn(
                                    'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium whitespace-nowrap transition-colors',
                                    active ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-foreground',
                                )}
                            >
                                <tab.icon className="size-3.5" />
                                {tab.label}
                            </Link>
                        );
                    })}
                </nav>
            </div>
            {children}
        </div>
    );
}
