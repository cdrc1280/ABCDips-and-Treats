import { Link, type InertiaLinkProps } from '@inertiajs/react';

import { cn } from '@/lib/utils';

export function TextLink({ className, ...props }: InertiaLinkProps) {
    return <Link className={cn('font-medium text-primary underline-offset-4 hover:underline', className)} {...props} />;
}
