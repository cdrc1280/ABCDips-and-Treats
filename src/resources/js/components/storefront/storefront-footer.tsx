import { Link, usePage } from '@inertiajs/react';
import { Mail, MapPin, Phone } from 'lucide-react';

import { cn } from '@/lib/utils';

const SHOP_LINKS = [
    { href: '/shop', label: 'The menu' },
    { href: '/best-sellers', label: 'Best sellers' },
    { href: '/featured', label: "Baker's picks" },
    { href: '/new-arrivals', label: 'New on the menu' },
    { href: '/custom-orders', label: 'Custom cakes' },
];

const COMPANY_LINKS = [
    { href: '/about', label: 'About us' },
    { href: '/blog', label: 'Blog & vlog' },
    { href: '/contact', label: 'Contact' },
    { href: '/faq', label: 'FAQ' },
    { href: '/privacy', label: 'Privacy policy' },
    { href: '/terms', label: 'Terms of service' },
    { href: '/suggestions', label: 'Suggest a Feature' },
];

/** Brand marks (lucide v1 ships no brand icons). */
function FacebookIcon() {
    return (
        <svg viewBox="0 0 24 24" className="size-4" fill="currentColor" aria-hidden="true">
            <path d="M13.5 21v-7.5h2.5l.4-3h-2.9V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 3.9v2.3H8v3h2.5V21h3z" />
        </svg>
    );
}

function InstagramIcon() {
    return (
        <svg viewBox="0 0 24 24" className="size-4" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="5" />
            <circle cx="12" cy="12" r="4" />
            <circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" stroke="none" />
        </svg>
    );
}

const socialClass =
    'flex size-9 items-center justify-center rounded-lg border border-sidebar-border bg-sidebar-accent text-sidebar-foreground transition-colors hover:text-sidebar-primary';
const linkClass = 'text-sm text-sidebar-muted-foreground transition-colors hover:text-sidebar-primary';
const headingClass = 'mb-4 text-[11px] font-semibold tracking-[0.1em] text-sidebar-primary uppercase';

/** Shop footer on the cocoa surface (the same tokens as the back-office sidebar). */
export default function StorefrontFooter() {
    const store = usePage().props.store;

    return (
        <footer className="mt-auto bg-sidebar text-sidebar-foreground">
            <div className="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-12 lg:gap-12 lg:px-8">
                <div className="grid content-start gap-5 md:col-span-6 lg:col-span-5">
                    <div className="flex items-center gap-3">
                        <img src="/images/logo.png" alt="" className="size-11 rounded-lg bg-sidebar-foreground/95 object-contain p-0.5" />
                        <span className="grid leading-tight">
                            <span className="font-display text-lg text-sidebar-accent-foreground">ABCDips &amp; Treats</span>
                            <span className="text-[11px] tracking-[0.08em] text-sidebar-muted-foreground uppercase">Bake with love</span>
                        </span>
                    </div>
                    <p className="max-w-sm text-sm leading-relaxed text-sidebar-muted-foreground">
                        A home bakery in Cavite. Banana bread, brownies, cookies and cakes, made with real butter.
                    </p>
                    <div className="flex items-center gap-2.5">
                        <a
                            href="https://www.facebook.com/abcdipsandtreats"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Facebook"
                            className={socialClass}
                        >
                            <FacebookIcon />
                        </a>
                        <a
                            href="https://www.instagram.com/abcdips_treats"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Instagram"
                            className={socialClass}
                        >
                            <InstagramIcon />
                        </a>
                    </div>
                </div>

                <div className="grid grid-cols-2 gap-8 md:col-span-6 lg:col-span-4">
                    {[
                        { title: 'Shop', links: SHOP_LINKS },
                        { title: 'Company', links: COMPANY_LINKS },
                    ].map((group) => (
                        <div key={group.title}>
                            <h3 className={headingClass}>{group.title}</h3>
                            <ul className="grid gap-2.5">
                                {group.links.map((link) => (
                                    <li key={link.href}>
                                        <Link href={link.href} className={linkClass}>
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>

                <div className="md:col-span-12 lg:col-span-3">
                    <div className="grid gap-3 rounded-xl border border-sidebar-border bg-sidebar-accent p-5">
                        <h3 className={cn(headingClass, 'mb-1')}>Visit &amp; Order</h3>
                        {store?.address && (
                            <p className="flex gap-2 text-sm text-sidebar-foreground/90">
                                <MapPin className="mt-0.5 size-4 shrink-0 text-sidebar-primary" />
                                {store.address}
                            </p>
                        )}
                        {store?.phone && (
                            <a href={`tel:${store.phone}`} className="flex gap-2 text-sm text-sidebar-foreground/90 hover:text-sidebar-primary">
                                <Phone className="mt-0.5 size-4 shrink-0 text-sidebar-primary" />
                                {store.phone}
                            </a>
                        )}
                        {store?.email && (
                            <a
                                href={`mailto:${store.email}`}
                                className="flex gap-2 text-sm break-all text-sidebar-foreground/90 hover:text-sidebar-primary"
                            >
                                <Mail className="mt-0.5 size-4 shrink-0 text-sidebar-primary" />
                                {store.email}
                            </a>
                        )}
                        <Link
                            href="/contact"
                            className="mt-1 inline-flex h-9 items-center justify-center rounded-md bg-sidebar-primary px-4 text-sm font-medium text-sidebar-primary-foreground transition-[filter] hover:brightness-105"
                        >
                            Send us a message
                        </Link>
                    </div>
                </div>
            </div>

            <div className="border-t border-sidebar-border">
                <div className="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-5 sm:flex-row sm:px-6 lg:px-8">
                    <p className="text-xs text-sidebar-muted-foreground">
                        &copy; {new Date().getFullYear()} ABCDips &amp; Treats. All rights reserved.
                    </p>
                    <div className="flex gap-5">
                        <Link href="/privacy" className="text-xs text-sidebar-muted-foreground hover:text-sidebar-primary">
                            Privacy Policy
                        </Link>
                        <Link href="/terms" className="text-xs text-sidebar-muted-foreground hover:text-sidebar-primary">
                            Terms of Service
                        </Link>
                    </div>
                </div>
            </div>
        </footer>
    );
}
