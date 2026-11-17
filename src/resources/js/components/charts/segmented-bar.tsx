import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

export interface Segment {
    key: string;
    label: string;
    value: number;
    /** Extra tooltip line, e.g. "₱12,400.00 revenue". */
    detail?: string;
}

/** Fixed series classes (chart-1…5). Colour follows the entity via `colorKeys`, never its rank. */
const SERIES = ['bg-chart-1', 'bg-chart-2', 'bg-chart-3', 'bg-chart-4', 'bg-chart-5'] as const;

interface Props {
    segments: Segment[];
    /** Entity keys in their fixed colour order; anything not listed (and "other") is neutral. */
    colorKeys: string[];
    unit: (value: number) => string;
    label: string;
    emptyText?: string;
}

/**
 * One whole split into parts: a tall (28px) bar, 2px gaps, 4px corners, no numbers inside.
 * The legend names the parts in block order with their share; hover/focus gives the detail.
 */
export function SegmentedBar({ segments, colorKeys, unit, label, emptyText = 'Nothing to show yet.' }: Props) {
    const total = segments.reduce((sum, s) => sum + s.value, 0);
    if (total === 0) return <p className="text-sm text-muted-foreground">{emptyText}</p>;

    const colour = (key: string) => {
        const index = colorKeys.indexOf(key);
        return index >= 0 && index < SERIES.length ? SERIES[index] : 'bg-chart-other';
    };
    const share = (value: number) => `${Math.round((value / total) * 100)}%`;
    const parts = segments.filter((s) => s.value > 0);

    return (
        <div className="grid gap-3">
            <div className="flex h-7 w-full gap-0.5" role="list" aria-label={label}>
                {parts.map((s) => (
                    <Tooltip key={s.key}>
                        <TooltipTrigger asChild>
                            <span
                                role="listitem"
                                tabIndex={0}
                                aria-label={`${s.label}: ${unit(s.value)}, ${share(s.value)}`}
                                className={cn(
                                    'min-w-1 rounded-[4px] transition-[filter] outline-none hover:brightness-110 focus-visible:ring-2 focus-visible:ring-ring',
                                    colour(s.key),
                                )}
                                style={{ flexGrow: s.value, flexBasis: 0 }}
                            />
                        </TooltipTrigger>
                        <TooltipContent className="grid gap-0.5">
                            <span className="font-semibold tabular-nums">
                                {unit(s.value)} · {share(s.value)}
                            </span>
                            <span className="opacity-80">{s.label}</span>
                            {s.detail && <span className="opacity-80">{s.detail}</span>}
                        </TooltipContent>
                    </Tooltip>
                ))}
            </div>
            <ul className="flex flex-wrap gap-x-4 gap-y-1.5 text-sm">
                {parts.map((s) => (
                    <li key={s.key} className="flex items-center gap-1.5">
                        <span className={cn('size-2.5 rounded-[3px]', colour(s.key))} aria-hidden="true" />
                        <span>{s.label}</span>
                        <span className="text-muted-foreground tabular-nums">{share(s.value)}</span>
                    </li>
                ))}
            </ul>
        </div>
    );
}
