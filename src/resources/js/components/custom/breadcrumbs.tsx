import { Link } from '@inertiajs/react';
import { Fragment } from 'react';

import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator } from '@/components/ui/breadcrumb';
import type { BreadcrumbItem as Crumb } from '@/types';

/** Always Home → Module → Page. */
export function Breadcrumbs({ items }: { items: Crumb[] }) {
    if (items.length === 0) return null;
    return (
        <Breadcrumb>
            <BreadcrumbList>
                {items.map((item, index) => {
                    const last = index === items.length - 1;
                    return (
                        <Fragment key={`${item.title}-${index}`}>
                            <BreadcrumbItem className={index < items.length - 2 ? 'hidden md:block' : undefined}>
                                {last || !item.href ? (
                                    <BreadcrumbPage>{item.title}</BreadcrumbPage>
                                ) : (
                                    <BreadcrumbLink asChild>
                                        <Link href={item.href}>{item.title}</Link>
                                    </BreadcrumbLink>
                                )}
                            </BreadcrumbItem>
                            {!last && <BreadcrumbSeparator className={index < items.length - 2 ? 'hidden md:block' : undefined} />}
                        </Fragment>
                    );
                })}
            </BreadcrumbList>
        </Breadcrumb>
    );
}
