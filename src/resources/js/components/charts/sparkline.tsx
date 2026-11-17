import { cn } from '@/lib/utils';

/**
 * Trend line for a stat tile: the series in the de-emphasis hue, the current (last)
 * point in the accent. Decorative: the tile states the numbers, so it is aria-hidden.
 */
export function Sparkline({ values, className, height = 36 }: { values: number[]; className?: string; height?: number }) {
    if (values.length < 2) return <div className={className} style={{ height }} />;

    const max = Math.max(...values);
    const min = Math.min(0, ...values);
    const span = max - min || 1;
    const pad = 4; // keeps the line and the end dot inside the box
    const yPct = (v: number) => pad + (1 - (v - min) / span) * (100 - pad * 2);
    const path = values.map((v, i) => `${i === 0 ? 'M' : 'L'}${((i / (values.length - 1)) * 100).toFixed(2)},${yPct(v).toFixed(2)}`).join(' ');
    const last = values[values.length - 1];

    return (
        <div className={cn('relative w-full', className)} style={{ height }} aria-hidden="true">
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" className="absolute inset-0 size-full overflow-visible">
                <path
                    d={path}
                    fill="none"
                    className="stroke-chart-other"
                    strokeWidth={2}
                    vectorEffect="non-scaling-stroke"
                    strokeLinejoin="round"
                    strokeLinecap="round"
                />
            </svg>
            <span
                className="absolute right-0 size-2 translate-x-1/2 -translate-y-1/2 rounded-full bg-chart-1 ring-2 ring-card"
                style={{ top: `${yPct(last)}%` }}
            />
        </div>
    );
}
