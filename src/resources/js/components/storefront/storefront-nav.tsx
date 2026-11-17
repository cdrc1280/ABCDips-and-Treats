import { Link, usePage } from '@inertiajs/react';
import { ChevronDown, Heart, LayoutDashboard, LogOut, Menu, Moon, Package, ShoppingBag, Sun, User } from 'lucide-react';
import { useEffect, useState } from 'react';

import { useStorefrontUi } from '@/components/storefront/storefront-context';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetDescription, SheetTitle } from '@/components/ui/sheet';
import { useAppearance } from '@/hooks/use-appearance';
import { useCart } from '@/hooks/use-cart';
import { cn } from '@/lib/utils';
import { login, logout } from '@/routes';

export const NAV_LINKS = [
    { href: '/', label: 'Home' },
    { href: '/shop', label: 'Menu' },
    { href: '/best-sellers', label: 'Best sellers' },
    { href: '/new-arrivals', label: 'New' },
    { href: '/custom-orders', label: 'Custom cakes' },
    { href: '/about', label: 'About' },
    { href: '/blog', label: 'Blog & vlog' },
] as const;

function isActive(current: string, href: string): boolean {
    const path = current.split('?')[0];
    return href === '/' ? path === '/' : path.startsWith(href);
}

/** Shop header: wordmark · section links · account, theme, basket. Phones get a sheet menu. */
export default function StorefrontNav() {
    const { url, props } = usePage();
    const user = props.auth.user;
    const wishlistCount = props.wishlist_ids.length;
    const { cart } = useCart();
    const { setCartOpen } = useStorefrontUi();
    const { isDark, updateAppearance } = useAppearance();
    const [scrolled, setScrolled] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 12);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    const accountLinks = [
        { href: '/account/orders', label: 'My Orders', icon: Package },
        { href: '/account/wishlist', label: wishlistCount ? `My Wishlist (${wishlistCount})` : 'My Wishlist', icon: Heart },
        { href: '/account/profile', label: 'My Profile', icon: User },
        ...(props.auth.can_access_admin ? [{ href: '/admin', label: 'Back Office', icon: LayoutDashboard }] : []),
    ];

    const themeLabel = isDark ? 'Switch to light mode' : 'Switch to dark mode';
    const toggleTheme = () => updateAppearance(isDark ? 'light' : 'dark');

    return (
        <>
            <header
                className={cn(
                    'fixed inset-x-0 top-0 z-50 border-b transition-[background-color,border-color] duration-300',
                    scrolled ? 'glass-surface border-border' : 'border-transparent bg-background',
                )}
            >
                <div className="mx-auto flex h-16 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <Link href="/" className="flex shrink-0 items-center gap-2.5" aria-label="ABCDips & Treats, home">
                        <img src="/images/logo.png" alt="" className="h-10 w-auto" />
                        <span className="hidden leading-none xl:grid">
                            <span className="font-display text-base">ABCDips &amp; Treats</span>
                            <span className="mt-1 text-[11px] tracking-[0.08em] text-muted-foreground uppercase">Bake with love</span>
                        </span>
                    </Link>

                    <nav aria-label="Shop sections" className="hidden flex-1 items-center justify-center gap-1 lg:flex xl:gap-2">
                        {NAV_LINKS.map((link) => {
                            const active = isActive(url, link.href);
                            return (
                                <Link
                                    key={link.href}
                                    href={link.href}
                                    prefetch
                                    aria-current={active ? 'page' : undefined}
                                    className={cn(
                                        'relative px-2 py-2 text-[13px] font-medium whitespace-nowrap transition-colors xl:text-sm',
                                        'after:absolute after:inset-x-2 after:bottom-0.5 after:h-0.5 after:rounded-full after:bg-primary after:transition-transform',
                                        active
                                            ? 'text-foreground after:scale-x-100'
                                            : 'text-muted-foreground after:scale-x-0 hover:text-foreground hover:after:scale-x-100',
                                    )}
                                >
                                    {link.label}
                                </Link>
                            );
                        })}
                    </nav>

                    <div className="flex shrink-0 items-center gap-1 sm:gap-1.5">
                        {user ? (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="ghost" size="sm" className="gap-1.5 px-2 sm:px-2.5" aria-label="Your account">
                                        <User />
                                        <span className="hidden max-w-[120px] truncate sm:inline">{user.name.split(' ')[0]}</span>
                                        <ChevronDown className="size-3 text-muted-foreground" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end" className="w-56">
                                    <DropdownMenuLabel className="font-normal">
                                        <p className="text-xs text-muted-foreground">Signed in as</p>
                                        <p className="truncate text-sm font-medium">{user.email}</p>
                                    </DropdownMenuLabel>
                                    <DropdownMenuSeparator />
                                    {accountLinks.map((item) => (
                                        <DropdownMenuItem key={item.href} asChild>
                                            <Link href={item.href}>
                                                <item.icon />
                                                {item.label}
                                            </Link>
                                        </DropdownMenuItem>
                                    ))}
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem variant="destructive" asChild>
                                        <Link href={logout().url} method="post" as="button" className="w-full">
                                            <LogOut />
                                            Sign Out
                                        </Link>
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        ) : (
                            <Button variant="ghost" size="sm" asChild className="hidden sm:inline-flex">
                                <Link href={login().url}>
                                    <User /> Sign In
                                </Link>
                            </Button>
                        )}

                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={toggleTheme}
                            aria-label={themeLabel}
                            title={themeLabel}
                            className="hidden sm:inline-flex"
                        >
                            {isDark ? <Sun /> : <Moon />}
                        </Button>

                        <Button
                            size="sm"
                            onClick={() => setCartOpen(true)}
                            aria-label={`Open basket, ${cart.item_count} items`}
                            className="gap-1.5 px-2.5 sm:px-3"
                        >
                            <ShoppingBag />
                            <span className="hidden sm:inline">Basket</span>
                            {cart.item_count > 0 && (
                                <span
                                    key={cart.item_count}
                                    className="spring-bounce -mr-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-highlight px-1 text-[11px] font-semibold text-highlight-foreground tabular-nums"
                                >
                                    {cart.item_count}
                                </span>
                            )}
                        </Button>

                        <Button variant="ghost" size="icon" onClick={() => setMobileOpen(true)} aria-label="Open menu" className="lg:hidden">
                            <Menu />
                        </Button>
                    </div>
                </div>
            </header>
            <div className="h-16" />

            <Sheet open={mobileOpen} onOpenChange={setMobileOpen}>
                <SheetContent side="right" className="w-[min(20rem,88vw)] gap-0 p-0">
                    <div className="border-b px-5 py-4">
                        <SheetTitle className="font-display text-lg font-normal">Menu</SheetTitle>
                        <SheetDescription className="sr-only">Shop sections and your account</SheetDescription>
                    </div>
                    <nav aria-label="Shop sections" className="grid gap-0.5 p-3">
                        {NAV_LINKS.map((link) => {
                            const active = isActive(url, link.href);
                            return (
                                <Link
                                    key={link.href}
                                    href={link.href}
                                    onClick={() => setMobileOpen(false)}
                                    aria-current={active ? 'page' : undefined}
                                    className={cn(
                                        'rounded-md px-3 py-2.5 text-base transition-colors',
                                        active
                                            ? 'bg-accent font-medium text-foreground'
                                            : 'text-muted-foreground hover:bg-accent hover:text-foreground',
                                    )}
                                >
                                    {link.label}
                                </Link>
                            );
                        })}
                    </nav>
                    <div className="mt-auto grid gap-0.5 border-t p-3">
                        {user ? (
                            <>
                                {accountLinks.map((item) => (
                                    <Link
                                        key={item.href}
                                        href={item.href}
                                        onClick={() => setMobileOpen(false)}
                                        className="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm hover:bg-accent"
                                    >
                                        <item.icon className="size-4 text-muted-foreground" />
                                        {item.label}
                                    </Link>
                                ))}
                                <Link
                                    href={logout().url}
                                    method="post"
                                    as="button"
                                    className="flex items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm text-destructive-ink hover:bg-destructive/10"
                                >
                                    <LogOut className="size-4" />
                                    Sign Out
                                </Link>
                            </>
                        ) : (
                            <Link
                                href={login().url}
                                onClick={() => setMobileOpen(false)}
                                className="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-primary hover:bg-accent"
                            >
                                <User className="size-4" />
                                Sign In / Create Account
                            </Link>
                        )}
                        <button
                            type="button"
                            onClick={toggleTheme}
                            className="flex items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm hover:bg-accent"
                        >
                            {isDark ? <Sun className="size-4 text-muted-foreground" /> : <Moon className="size-4 text-muted-foreground" />}
                            {themeLabel}
                        </button>
                    </div>
                </SheetContent>
            </Sheet>
        </>
    );
}
