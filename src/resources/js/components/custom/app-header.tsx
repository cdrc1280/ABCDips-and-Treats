import { Store } from 'lucide-react';

import { AppearanceToggle } from '@/components/custom/appearance-toggle';
import { Breadcrumbs } from '@/components/custom/breadcrumbs';
import { NotificationBell } from '@/components/custom/notification-bell';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import type { BreadcrumbItem } from '@/types';

/** Sticky top bar inside the cream panel: menu toggle · breadcrumbs · shop link, bell, theme. */
export function AppHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItem[] }) {
    return (
        <header className="sticky top-0 z-20 flex h-14 shrink-0 items-center gap-2 border-b bg-background/85 px-3 backdrop-blur-md md:rounded-t-xl md:px-4">
            <SidebarTrigger className="-ml-1" />
            <Separator orientation="vertical" className="mr-1 data-[orientation=vertical]:h-4" />
            <div className="min-w-0 flex-1 overflow-hidden">
                <Breadcrumbs items={breadcrumbs} />
            </div>
            <div className="flex shrink-0 items-center gap-0.5">
                <Tooltip>
                    <TooltipTrigger asChild>
                        <Button variant="ghost" size="icon" asChild>
                            <a href="/" target="_blank" rel="noopener" aria-label="Open the shop in a new tab">
                                <Store />
                            </a>
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent>Open the shop</TooltipContent>
                </Tooltip>
                <NotificationBell />
                <AppearanceToggle />
            </div>
        </header>
    );
}
