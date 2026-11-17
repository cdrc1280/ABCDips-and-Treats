import { usePage } from '@inertiajs/react';
import { useState, type ReactNode } from 'react';

import { FooterSlotContext } from '@/components/custom/app-footer';
import { AppHeader } from '@/components/custom/app-header';
import { AppSidebar } from '@/components/custom/app-sidebar';
import { PageTransition } from '@/components/custom/page-transition';
import { SidebarInset, SidebarProvider } from '@/components/ui/sidebar';
import { Toaster } from '@/components/ui/sonner';
import { TooltipProvider } from '@/components/ui/tooltip';
import { useFlashToast } from '@/hooks/use-flash-toast';
import type { BreadcrumbItem } from '@/types';

/** Back-office shell: sidebar · header (breadcrumbs, theme) · page · sticky footer slot. */
export default function AppLayout({ children, breadcrumbs = [] }: { children: ReactNode; breadcrumbs?: BreadcrumbItem[] }) {
    const { sidebarOpen } = usePage().props;
    const url = usePage().url;
    const [footerSlot, setFooterSlot] = useState<HTMLDivElement | null>(null);
    useFlashToast();

    return (
        <TooltipProvider delayDuration={200}>
            <SidebarProvider defaultOpen={sidebarOpen}>
                <AppSidebar />
                <SidebarInset className="min-w-0 md:peer-data-[variant=inset]:shadow-none">
                    <FooterSlotContext value={footerSlot}>
                        <AppHeader breadcrumbs={breadcrumbs} />
                        <div className="flex-1 px-4 py-5 md:px-6 md:py-6">
                            <PageTransition key={url.split('?')[0]}>{children}</PageTransition>
                        </div>
                    </FooterSlotContext>
                    <div ref={setFooterSlot} className="sticky bottom-0 z-20" />
                </SidebarInset>
            </SidebarProvider>
            <Toaster position="top-right" richColors closeButton />
        </TooltipProvider>
    );
}
