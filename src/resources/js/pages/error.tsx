import { Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';

const messages: Record<number, { title: string; body: string }> = {
    403: { title: 'Access denied', body: "You don't have permission to open this page." },
    404: { title: 'Page not found', body: "The page you're looking for doesn't exist or was moved." },
    500: { title: 'Something went wrong', body: 'An unexpected error occurred on our side. Please try again in a moment.' },
    503: { title: 'Down for maintenance', body: "We're doing a little baking behind the scenes. Please check back soon." },
};

/** One branded page for 403 / 404 / 500 / 503. Rendered without the app shell. */
export default function ErrorPage({ status }: { status: number }) {
    const message = messages[status] ?? messages[500];
    const isAdmin = typeof window !== 'undefined' && window.location.pathname.startsWith('/admin');

    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 text-center">
            <Head title={message.title} />
            <img src="/images/logo.png" alt="ABCDips & Treats" className="size-16 rounded-lg object-contain" />
            <p className="font-display text-7xl text-primary tabular-nums">{status}</p>
            <div className="grid max-w-md gap-2">
                <h1 className="text-xl font-semibold">{message.title}</h1>
                <p className="text-sm text-muted-foreground">{message.body}</p>
            </div>
            <Button asChild>
                <Link href={isAdmin ? '/admin' : '/'}>{isAdmin ? 'Back to dashboard' : 'Back to the menu'}</Link>
            </Button>
        </div>
    );
}

ErrorPage.layout = (page: React.ReactNode) => page;
