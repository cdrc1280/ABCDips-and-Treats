import { Link } from '@inertiajs/react';
import { ArrowLeft, Eraser, type LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';

import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';

interface Props {
    backHref: string;
    backLabel: string;
    icon: LucideIcon;
    title: string;
    description?: string;
    onClear?: () => void;
    clearDisabled?: boolean;
    /** Right-hand content instead of Clear (edit pages: record switcher, menu). */
    actions?: ReactNode;
}

/** Back arrow · icon avatar · title + description · Clear (disabled until dirty). */
export function CreateHeader({ backHref, backLabel, icon: Icon, title, description, onClear, clearDisabled, actions }: Props) {
    return (
        <div className="flex min-w-0 flex-wrap items-center gap-3">
            <Tooltip>
                <TooltipTrigger asChild>
                    <Button variant="ghost" size="icon" asChild aria-label={backLabel}>
                        <Link href={backHref}>
                            <ArrowLeft />
                        </Link>
                    </Button>
                </TooltipTrigger>
                <TooltipContent>{backLabel}</TooltipContent>
            </Tooltip>
            <span className="flex size-10 shrink-0 items-center justify-center rounded-lg bg-highlight/25 text-primary">
                <Icon className="size-5" />
            </span>
            {/* min-w-40: keeps the title readable on phones; wide actions (record switcher) wrap below instead */}
            <div className="min-w-40 flex-1">
                <h1 className="truncate font-display text-xl leading-tight tracking-tight md:text-2xl">{title}</h1>
                {description && <p className="truncate text-sm text-muted-foreground">{description}</p>}
            </div>
            {actions ??
                (onClear && (
                    <Button variant="outline" size="sm" onClick={onClear} disabled={clearDisabled}>
                        <Eraser /> Clear
                    </Button>
                ))}
        </div>
    );
}
