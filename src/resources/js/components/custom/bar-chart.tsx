import { cn } from '@/lib/utils';

export interface BarDatum {
    label: string;
    value: number;
    /** Optional secondary line in the tooltip, e.g. "12 orders". */
    detail?: string;
}

interface Props {
    data: BarDatum[];
    format?: (value: number) => string;
    orientation?: 'vertical' | 'horizontal';
    className?: string;
    emptyText?: string;
}

/**
 * Dependency-free bar chart in the first series colour. Bars: square at the baseline, 4px
 * rounded data end, never wider than 24px. Bars carry an accessible
 * label with the exact value; the visual height/width is relative to the largest bar.
 */
export function BarChart({
    data,
    format = (v) => v.toLocaleString('en-US'),
    orientation = 'vertical',
    className,
    emptyText = 'No data yet.',
}: Props) {
    const max = Math.max(0, ...data.map((d) => d.value));
    if (data.length === 0 || max === 0) {
        return <div className={cn('flex h-48 items-center justify-center text-sm text-muted-foreground', className)}>{emptyText}</div>;
    }

    if (orientation === 'horizontal') {
        return (
            <ul className={cn('grid gap-3', className)}>
                {data.map((d) => (
                    <li key={d.label} className="grid gap-1">
                        <div className="flex justify-between gap-2 text-sm">
                            <span className="truncate">{d.label}</span>
                            <span className="shrink-0 font-medium tabular-nums">
                                {format(d.value)}
                                {d.detail && <span className="font-normal text-muted-foreground"> · {d.detail}</span>}
                            </span>
                        </div>
                        <div
                            className="h-2.5 rounded-r-[4px] bg-chart-1 transition-[width] duration-500"
                            style={{ width: `${Math.max(1, (d.value / max) * 100)}%` }}
                            aria-hidden="true"
                        />
                    </li>
                ))}
            </ul>
        );
    }

    return (
        <div className={cn('flex h-56 items-end gap-1.5 sm:gap-2', className)} role="list">
            {data.map((d) => (
                <div key={d.label} role="listitem" className="group flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-1.5">
                    <div className="relative flex w-full flex-1 items-end">
                        <div
                            className="mx-auto w-full max-w-6 rounded-t-[4px] bg-chart-1 transition-[height,filter] duration-500 group-hover:brightness-110"
                            style={{ height: `${Math.max(2, (d.value / max) * 100)}%` }}
                            aria-label={`${d.label}: ${format(d.value)}${d.detail ? `, ${d.detail}` : ''}`}
                        />
                        <div className="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1 hidden -translate-x-1/2 rounded-md border bg-popover px-2 py-1 text-xs whitespace-nowrap shadow-md group-hover:block">
                            <div className="font-medium tabular-nums">{format(d.value)}</div>
                            {d.detail && <div className="text-muted-foreground">{d.detail}</div>}
                        </div>
                    </div>
                    <span className="truncate text-[11px] text-muted-foreground">{d.label}</span>
                </div>
            ))}
        </div>
    );
}
