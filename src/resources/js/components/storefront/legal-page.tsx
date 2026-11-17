import { Head } from '@inertiajs/react';

import { PageHeader } from '@/components/storefront/page-header';
import { formatDate } from '@/lib/format';

export interface LegalProps {
    store: { name: string; email: string; address: string };
    updated: string;
}

export function LegalPage({ title, sections, store, updated }: LegalProps & { title: string; sections: { title: string; content: string }[] }) {
    return (
        <>
            <Head title={title} />
            <div className="mx-auto max-w-3xl px-4 pb-20">
                <PageHeader title={title} subtitle={`Last updated ${formatDate(updated)}`} />
                <article className="space-y-8 rounded-2xl border border-primary/20 bg-card p-6 shadow-sm sm:p-10">
                    {sections.map((section) => (
                        <section key={section.title}>
                            <h2 className="text-lg font-bold text-foreground">{section.title}</h2>
                            <p className="mt-2 text-sm leading-relaxed text-muted-foreground">{section.content}</p>
                        </section>
                    ))}
                    <p className="border-t border-primary/20 pt-6 text-sm text-muted-foreground">
                        Questions? Contact {store.name}
                        {store.email && (
                            <>
                                {' '}
                                at{' '}
                                <a href={`mailto:${store.email}`} className="font-semibold text-primary hover:underline">
                                    {store.email}
                                </a>
                            </>
                        )}
                        {store.address && <> · {store.address}</>}.
                    </p>
                </article>
            </div>
        </>
    );
}
