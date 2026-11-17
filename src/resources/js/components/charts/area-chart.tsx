import { useLayoutEffect, useRef, useState, type KeyboardEvent, type PointerEvent } from 'react';

import { cn } from '@/lib/utils';

export interface AreaPoint {
    /** Short axis label ("Nov 3", "Mar"). */
    label: string;
    /** Tooltip heading ("Tue, Nov 3"). */
    full: string;
    value: number;
    /** Secondary tooltip line ("12 orders"). */
    detail?: string;
}

interface Props {
    points: AreaPoint[];
    format: (value: number) => string;
    formatTick?: (value: number) => string;
    height?: number;
    label: string;
    emptyText?: string;
    className?: string;
}

const PAD = { top: 12, right: 8, bottom: 24, left: 44 };

/** Round the top of the axis up to 1 / 2 / 2.5 / 5 × 10ⁿ so ticks read cleanly. */
function niceMax(value: number): number {
    if (value <= 0) return 1;
    const power = 10 ** Math.floor(Math.log10(value));
    const step = [1, 2, 2.5, 5, 10].find((s) => s * power >= value) ?? 10;
    return step * power;
}

function useWidth<T extends HTMLElement>() {
    const ref = useRef<T>(null);
    const [width, setWidth] = useState(0);
    useLayoutEffect(() => {
        const el = ref.current;
        if (!el) return;
        const observer = new ResizeObserver(([entry]) => setWidth(Math.floor(entry.contentRect.width)));
        observer.observe(el);
        return () => observer.disconnect();
    }, []);
    return [ref, width] as const;
}

/**
 * Single-series area chart: 2px line, 10% wash, hairline grid, crosshair + tooltip that
 * snaps to the nearest point. Arrow keys move the crosshair when the chart has focus.
 */
export function AreaChart({ points, format, formatTick = format, height = 220, label, emptyText = 'Nothing to show yet.', className }: Props) {
    const [ref, width] = useWidth<HTMLDivElement>();
    const [active, setActive] = useState<number | null>(null);

    const max = Math.max(0, ...points.map((p) => p.value));
    const top = niceMax(max);
    const innerW = Math.max(0, width - PAD.left - PAD.right);
    const innerH = height - PAD.top - PAD.bottom;
    const n = points.length;

    const x = (i: number) => PAD.left + (n <= 1 ? innerW / 2 : (i / (n - 1)) * innerW);
    const y = (v: number) => PAD.top + innerH - (v / top) * innerH;

    const line = points.map((p, i) => `${i === 0 ? 'M' : 'L'}${x(i).toFixed(1)},${y(p.value).toFixed(1)}`).join(' ');
    const area = n > 0 ? `${line} L${x(n - 1).toFixed(1)},${PAD.top + innerH} L${x(0).toFixed(1)},${PAD.top + innerH} Z` : '';
    const ticks = [0, top / 2, top];
    // Every Nth label (about one per 72px), always the last one, never one crowding the last.
    const labelStep = Math.max(1, Math.ceil(n / Math.max(2, Math.floor(innerW / 72))));
    const showLabel = (i: number) => i === n - 1 || (i % labelStep === 0 && n - 1 - i >= labelStep / 2);

    const nearest = (event: PointerEvent<SVGSVGElement>) => {
        const rect = event.currentTarget.getBoundingClientRect();
        const ratio = innerW > 0 ? (event.clientX - rect.left - PAD.left) / innerW : 0;
        setActive(Math.min(n - 1, Math.max(0, Math.round(ratio * (n - 1)))));
    };

    const onKey = (event: KeyboardEvent<SVGSVGElement>) => {
        const current = active ?? n - 1;
        const next = { ArrowLeft: current - 1, ArrowRight: current + 1, Home: 0, End: n - 1 }[event.key];
        if (next === undefined) return;
        event.preventDefault();
        setActive(Math.min(n - 1, Math.max(0, next)));
    };

    if (n === 0 || max === 0) {
        return (
            <div className={cn('flex items-center justify-center text-sm text-muted-foreground', className)} style={{ height }}>
                {emptyText}
            </div>
        );
    }

    const point = active === null ? null : points[active];
    const tipX = active === null ? 0 : x(active);
    const tipAlign = tipX < 90 ? 'left' : tipX > width - 90 ? 'right' : 'center';

    return (
        <div ref={ref} className={cn('relative w-full select-none', className)} style={{ height }}>
            {width > 0 && (
                <svg
                    width={width}
                    height={height}
                    role="img"
                    aria-label={label}
                    tabIndex={0}
                    className="block touch-pan-y rounded-md outline-none focus-visible:ring-2 focus-visible:ring-ring/60"
                    onPointerMove={nearest}
                    onPointerDown={nearest}
                    onPointerLeave={() => setActive(null)}
                    onFocus={() => setActive(n - 1)}
                    onBlur={() => setActive(null)}
                    onKeyDown={onKey}
                >
                    {ticks.map((t) => (
                        <g key={t}>
                            <line x1={PAD.left} x2={width - PAD.right} y1={y(t)} y2={y(t)} className="stroke-chart-grid" strokeWidth={1} />
                            <text x={PAD.left - 8} y={y(t)} dy="0.32em" textAnchor="end" className="fill-muted-foreground text-[11px] tabular-nums">
                                {formatTick(t)}
                            </text>
                        </g>
                    ))}

                    <path d={area} className="fill-chart-1/10" />
                    <path d={line} fill="none" className="stroke-chart-1" strokeWidth={2} strokeLinejoin="round" strokeLinecap="round" />

                    {points.map((p, i) =>
                        showLabel(i) ? (
                            <text
                                key={p.label + i}
                                x={x(i)}
                                y={height - 6}
                                textAnchor={i === 0 ? 'start' : i === n - 1 ? 'end' : 'middle'}
                                className="fill-muted-foreground text-[11px]"
                            >
                                {p.label}
                            </text>
                        ) : null,
                    )}

                    {point && active !== null && (
                        <g>
                            <line x1={tipX} x2={tipX} y1={PAD.top} y2={PAD.top + innerH} className="stroke-muted-foreground/50" strokeWidth={1} />
                            <circle cx={tipX} cy={y(point.value)} r={4.5} className="fill-chart-1 stroke-card" strokeWidth={2} />
                        </g>
                    )}
                </svg>
            )}

            {point && (
                <div
                    role="status"
                    className={cn(
                        'pointer-events-none absolute top-0 z-10 grid min-w-32 gap-0.5 rounded-md border bg-popover px-2.5 py-1.5 text-xs shadow-md',
                        tipAlign === 'center' && '-translate-x-1/2',
                        tipAlign === 'right' && '-translate-x-full',
                    )}
                    style={{ left: tipX }}
                >
                    <span className="text-sm font-semibold text-popover-foreground tabular-nums">{format(point.value)}</span>
                    <span className="text-muted-foreground">{point.full}</span>
                    {point.detail && <span className="text-muted-foreground">{point.detail}</span>}
                </div>
            )}
        </div>
    );
}
