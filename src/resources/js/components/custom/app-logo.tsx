import { cn } from '@/lib/utils';

/** Back-office wordmark; sits on the cocoa sidebar, so it uses the sidebar tokens. */
export function AppLogo({ className, compact = false }: { className?: string; compact?: boolean }) {
    return (
        <span className={cn('flex items-center gap-2.5', className)}>
            <img src="/images/logo.png" alt="" className="size-8 shrink-0 rounded-md bg-sidebar-foreground/95 object-contain p-0.5" />
            {!compact && (
                <span className="grid text-left leading-tight">
                    <span className="truncate font-display text-[15px] text-sidebar-accent-foreground">ABCDips &amp; Treats</span>
                    <span className="truncate text-[11px] tracking-[0.08em] text-sidebar-muted-foreground uppercase">Back office</span>
                </span>
            )}
        </span>
    );
}
