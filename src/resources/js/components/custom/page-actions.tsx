import { Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import { usePermissions } from '@/hooks/use-permissions';
import type { PageAction } from '@/types';

/** Header buttons from schemas/{thing}/actions.ts; actions without permission are hidden, not disabled. */
export function PageActions({ actions }: { actions: PageAction[] }) {
    const { can } = usePermissions();
    const visible = actions.filter((action) => can(action.permission));
    if (visible.length === 0) return null;

    return (
        <div className="flex flex-wrap items-center gap-2">
            {visible.map((action) => {
                const Icon = action.icon;
                const content = (
                    <>
                        {Icon && <Icon />}
                        {action.label}
                    </>
                );
                if (action.href) {
                    return (
                        <Button key={action.label} size="sm" variant={action.variant ?? 'default'} asChild>
                            {action.external ? <a href={action.href}>{content}</a> : <Link href={action.href}>{content}</Link>}
                        </Button>
                    );
                }
                return (
                    <Button key={action.label} size="sm" variant={action.variant ?? 'default'} onClick={action.onClick}>
                        {content}
                    </Button>
                );
            })}
        </div>
    );
}
