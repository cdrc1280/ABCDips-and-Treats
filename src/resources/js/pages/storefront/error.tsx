import { Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';

const messages: Record<number, { title: string; body: string }> = {
    403: { title: 'Not allowed', body: "You don't have access to this page." },
    404: { title: 'This page crumbled', body: "We couldn't find what you were looking for. It may have sold out or moved." },
    500: { title: 'Something burnt in the oven', body: 'An unexpected error happened on our side. Please try again in a moment.' },
    503: { title: 'Back in a bit', body: "We're restocking the shelves. Please check back soon." },
};

export default function StorefrontError({ status }: { status: number }) {
    const message = messages[status] ?? messages[500];
    return (
        <div className="mx-auto flex max-w-xl flex-col items-center gap-5 px-6 py-24 text-center">
            <Head title={message.title} />
            <p className="font-display text-7xl text-primary tabular-nums">{status}</p>
            <h1 className="text-2xl font-bold">{message.title}</h1>
            <p className="text-muted-foreground">{message.body}</p>
            <Button asChild>
                <Link href="/">Back to the menu</Link>
            </Button>
        </div>
    );
}
