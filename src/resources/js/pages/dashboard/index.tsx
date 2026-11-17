import { Deferred, Head, Link, router } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowRight,
    FileSpreadsheet,
    FileText,
    Loader2,
    PackageMinus,
    Table2,
    TrendingDown,
    TrendingUp,
    ChartArea,
} from 'lucide-react';
import { useState, type ReactNode } from 'react';
import { toast } from 'sonner';

import { AreaChart } from '@/components/charts/area-chart';
import { SegmentedBar } from '@/components/charts/segmented-bar';
import { Sparkline } from '@/components/charts/sparkline';
import { BarChart } from '@/components/custom/bar-chart';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import { usePermissions } from '@/hooks/use-permissions';
import { compactMoney, formatDate, formatNumber, money } from '@/lib/format';
import { downloadFile } from '@/lib/http';
import { ReportPermission } from '@/lib/permissions';
import { cn } from '@/lib/utils';
import { StatusBadge, toneFor } from '@/renderers/cells/badges';
import { dashboard } from '@/routes/admin';
import { edit as editOrder, index as ordersIndex } from '@/routes/admin/orders';
import { download } from '@/routes/admin/reports';

interface Totals {
    revenue: number;
    orders: number;
    average: number;
}

interface Analytics {
    period: string;
    label: string;
    granularity: 'day' | 'month';
    points: { key: string; label: string; full: string; revenue: number; orders: number }[];
    totals: Totals;
    previous: Totals;
}

interface MixRow {
    key: string;
    label: string;
    orders: number;
    revenue: number;
}

interface Props {
    period: string;
    periods: { value: string; label: string }[];
    analytics?: Analytics;
    categories?: { name: string; revenue: number }[];
    top_products?: { name: string; qty: number; revenue: number }[];
    mix?: { payment: MixRow[]; fulfilment: MixRow[] };
    pipeline?: { status: string; label: string; count: number }[];
    low_stock?: { kind: string; name: string; stock: number; minimum: number; unit: string }[];
    recent_orders?: { id: string; order_number: string; customer_name: string; total: number; status: string; created_at: string | null }[];
    reports: { key: string; title: string; description: string }[];
}

/** Props the period filter re-fetches; the live group (pipeline, stock, latest orders) stays as is. */
const PERIOD_PROPS = ['period', 'analytics', 'categories', 'top_products', 'mix'];

/** Fixed colour order per entity, so a method keeps its colour when the ranking changes. */
const PAYMENT_COLOURS = ['gcash', 'maya', 'qrph', 'cash', 'bank_transfer'];
const FULFILMENT_COLOURS = ['pickup', 'delivery'];

const pesos = (value: number) => `₱${Math.round(value).toLocaleString('en-US')}`;

function Tile({ title, aside, className, children }: { title: string; aside?: ReactNode; className?: string; children: ReactNode }) {
    return (
        <section className={cn('flex min-w-0 flex-col gap-4 rounded-xl border bg-card p-4 text-card-foreground md:p-5', className)}>
            <header className="flex items-center justify-between gap-3">
                <h2 className="text-sm font-medium text-muted-foreground">{title}</h2>
                {aside}
            </header>
            {children}
        </section>
    );
}

function LiveTag() {
    return (
        <span className="inline-flex items-center gap-1.5 text-xs text-muted-foreground">
            <span className="size-1.5 rounded-full bg-success" aria-hidden="true" />
            Right now
        </span>
    );
}

/** Signed change vs the previous period of the same length. Up is good for every metric here. */
function Delta({ current, previous, periodLabel }: { current: number; previous: number; periodLabel: string }) {
    const against = `vs previous ${periodLabel.replace(/^Last /, '')}`;
    if (previous === 0) {
        return <p className="text-xs text-muted-foreground">{current > 0 ? `Nothing to compare ${against}` : 'No sales yet'}</p>;
    }
    const change = ((current - previous) / previous) * 100;
    const up = change >= 0;
    const Icon = up ? TrendingUp : TrendingDown;
    return (
        <p className="flex flex-wrap items-center gap-x-1.5 text-xs">
            <span className={cn('inline-flex items-center gap-1 font-semibold', up ? 'text-success-ink' : 'text-destructive-ink')}>
                <Icon className="size-3.5" aria-hidden="true" />
                {up ? '+' : '−'}
                {Math.abs(change).toFixed(Math.abs(change) < 10 ? 1 : 0)}%
            </span>
            <span className="text-muted-foreground">{against}</span>
        </p>
    );
}

function Kpi({ value, delta, trend }: { value: string; delta: ReactNode; trend: number[] }) {
    return (
        <div className="grid gap-3">
            <div className="grid gap-1">
                <p className="text-3xl font-semibold tracking-tight">{value}</p>
                {delta}
            </div>
            <Sparkline values={trend} />
        </div>
    );
}

const rows = (count: number, height = 'h-6') => (
    <div className="grid gap-3">
        {Array.from({ length: count }).map((_, i) => (
            <Skeleton key={i} className={cn(height, 'w-full')} />
        ))}
    </div>
);

const kpiSkeleton = (
    <div className="grid gap-3">
        <Skeleton className="h-9 w-32" />
        <Skeleton className="h-4 w-40" />
        <Skeleton className="h-9 w-full" />
    </div>
);

export default function Dashboard({ period, periods, analytics, categories, top_products, mix, pipeline, low_stock, recent_orders, reports }: Props) {
    const { can } = usePermissions();
    const [requested, setRequested] = useState<string | null>(null);
    const [asTable, setAsTable] = useState(false);
    const reloading = requested !== null;
    const canExport = can(ReportPermission.ReportsExport);

    const changePeriod = (value: string) => {
        if (!value || value === period) return;
        router.reload({
            data: { period: value },
            only: PERIOD_PROPS,
            onStart: () => setRequested(value),
            onFinish: () => setRequested(null),
        });
    };

    // Refetch keeps the frame: tiles hold their last render, dimmed, instead of flashing skeletons.
    const scoped = cn('transition-opacity duration-200', reloading && 'opacity-55');
    const periodLabel = analytics?.label ?? periods.find((p) => p.value === period)?.label ?? '';
    const openOrders = (pipeline ?? []).reduce((sum, stage) => sum + stage.count, 0);

    return (
        <>
            <Head title="Dashboard" />
            <div className="grid gap-5">
                <header className="grid gap-4">
                    <div>
                        <h1 className="font-display text-3xl tracking-tight">Dashboard</h1>
                        <p className="text-sm text-muted-foreground">
                            Sales, kitchen and stock. Cancelled, refunded and archived orders are left out of sales.
                        </p>
                    </div>
                    <div className="flex items-center gap-3 overflow-x-auto">
                        <span className="shrink-0 text-sm text-muted-foreground">Showing</span>
                        <ToggleGroup
                            type="single"
                            variant="outline"
                            size="sm"
                            value={requested ?? period}
                            onValueChange={changePeriod}
                            aria-label="Period"
                        >
                            {periods.map((p) => (
                                <ToggleGroupItem key={p.value} value={p.value} className="px-3 whitespace-nowrap">
                                    {p.label.replace(/^Last /, '')}
                                </ToggleGroupItem>
                            ))}
                        </ToggleGroup>
                    </div>
                </header>

                <div className="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                    {/* Revenue: the one hero tile */}
                    <Tile
                        title={`Revenue · ${periodLabel.toLowerCase()}`}
                        className={cn('md:col-span-2 xl:row-span-2', scoped)}
                        aside={
                            <Button
                                variant="ghost"
                                size="sm"
                                className="h-7 gap-1.5 text-xs"
                                onClick={() => setAsTable((v) => !v)}
                                aria-pressed={asTable}
                            >
                                {asTable ? <ChartArea className="size-3.5" /> : <Table2 className="size-3.5" />}
                                {asTable ? 'Chart' : 'Table'}
                            </Button>
                        }
                    >
                        <Deferred
                            data="analytics"
                            fallback={
                                <div className="grid gap-3">
                                    <Skeleton className="h-12 w-48" />
                                    <Skeleton className="h-4 w-56" />
                                    <Skeleton className="h-56 w-full" />
                                </div>
                            }
                        >
                            {analytics && (
                                <div className="grid gap-4">
                                    <div className="grid gap-1.5">
                                        <p className="text-5xl font-semibold tracking-tight">{pesos(analytics.totals.revenue)}</p>
                                        <Delta
                                            current={analytics.totals.revenue}
                                            previous={analytics.previous.revenue}
                                            periodLabel={analytics.label}
                                        />
                                    </div>
                                    {asTable ? (
                                        <div className="max-h-64 overflow-y-auto rounded-md border">
                                            <table className="w-full text-sm">
                                                <thead className="sticky top-0 bg-muted text-left text-xs text-muted-foreground">
                                                    <tr>
                                                        <th className="px-3 py-2 font-medium">
                                                            {analytics.granularity === 'month' ? 'Month' : 'Day'}
                                                        </th>
                                                        <th className="px-3 py-2 text-right font-medium">Orders</th>
                                                        <th className="px-3 py-2 text-right font-medium">Revenue</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y">
                                                    {[...analytics.points].reverse().map((p) => (
                                                        <tr key={p.key}>
                                                            <td className="px-3 py-1.5">{p.full}</td>
                                                            <td className="px-3 py-1.5 text-right tabular-nums">{formatNumber(p.orders)}</td>
                                                            <td className="px-3 py-1.5 text-right tabular-nums">{money(p.revenue)}</td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    ) : (
                                        <AreaChart
                                            label={`Revenue per ${analytics.granularity}, ${analytics.label.toLowerCase()}`}
                                            points={analytics.points.map((p) => ({
                                                label: p.label,
                                                full: p.full,
                                                value: p.revenue,
                                                detail: `${formatNumber(p.orders)} ${p.orders === 1 ? 'order' : 'orders'}`,
                                            }))}
                                            format={money}
                                            formatTick={compactMoney}
                                            emptyText="No sales in this period yet."
                                        />
                                    )}
                                </div>
                            )}
                        </Deferred>
                    </Tile>

                    <Tile title="Orders" className={scoped}>
                        <Deferred data="analytics" fallback={kpiSkeleton}>
                            {analytics && (
                                <Kpi
                                    value={formatNumber(analytics.totals.orders)}
                                    delta={
                                        <Delta current={analytics.totals.orders} previous={analytics.previous.orders} periodLabel={analytics.label} />
                                    }
                                    trend={analytics.points.map((p) => p.orders)}
                                />
                            )}
                        </Deferred>
                    </Tile>

                    <Tile title="Average order" className={scoped}>
                        <Deferred data="analytics" fallback={kpiSkeleton}>
                            {analytics && (
                                <Kpi
                                    value={pesos(analytics.totals.average)}
                                    delta={
                                        <Delta
                                            current={analytics.totals.average}
                                            previous={analytics.previous.average}
                                            periodLabel={analytics.label}
                                        />
                                    }
                                    trend={analytics.points.map((p) => (p.orders > 0 ? p.revenue / p.orders : 0))}
                                />
                            )}
                        </Deferred>
                    </Tile>

                    <Tile
                        title={pipeline ? `Open orders · ${formatNumber(openOrders)}` : 'Open orders'}
                        className="md:col-span-2"
                        aside={<LiveTag />}
                    >
                        <Deferred data="pipeline" fallback={rows(2, 'h-14')}>
                            <div className="grid gap-3">
                                <ol className="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    {(pipeline ?? []).map((stage, i) => (
                                        <li key={stage.status} className="grid gap-0.5 rounded-lg bg-muted/60 px-3 py-2.5">
                                            <span
                                                className={cn('text-2xl font-semibold tabular-nums', stage.count === 0 && 'text-muted-foreground/70')}
                                            >
                                                {formatNumber(stage.count)}
                                            </span>
                                            <span className="truncate text-xs text-muted-foreground">
                                                {i + 1}. {stage.label}
                                            </span>
                                        </li>
                                    ))}
                                </ol>
                                <Link
                                    href={ordersIndex().url}
                                    className="inline-flex items-center gap-1 justify-self-start text-sm font-medium text-primary hover:underline"
                                >
                                    View orders <ArrowRight className="size-3.5" />
                                </Link>
                            </div>
                        </Deferred>
                    </Tile>

                    <Tile title="Best sellers · by units" className={cn('md:col-span-1 xl:col-span-2', scoped)}>
                        <Deferred data="top_products" fallback={rows(5, 'h-8')}>
                            <BarChart
                                orientation="horizontal"
                                data={(top_products ?? []).map((p) => ({ label: p.name, value: p.qty, detail: compactMoney(p.revenue) }))}
                                format={(v) => `${formatNumber(v)} sold`}
                                emptyText="No sales in this period yet."
                            />
                        </Deferred>
                    </Tile>

                    <Tile title="Revenue by category" className={cn('md:col-span-1 xl:col-span-2', scoped)}>
                        <Deferred data="categories" fallback={rows(5, 'h-8')}>
                            <BarChart
                                orientation="horizontal"
                                data={(categories ?? []).map((c) => ({ label: c.name, value: c.revenue }))}
                                format={money}
                                emptyText="No sales in this period yet."
                            />
                        </Deferred>
                    </Tile>

                    <Tile title="How customers paid" className={cn('md:col-span-2', scoped)}>
                        <Deferred data="mix" fallback={rows(2, 'h-7')}>
                            <SegmentedBar
                                label="Orders by payment method"
                                segments={(mix?.payment ?? []).map((m) => ({
                                    key: m.key,
                                    label: m.label,
                                    value: m.orders,
                                    detail: `${money(m.revenue)} revenue`,
                                }))}
                                colorKeys={PAYMENT_COLOURS}
                                unit={(v) => `${formatNumber(v)} ${v === 1 ? 'order' : 'orders'}`}
                                emptyText="No orders in this period yet."
                            />
                        </Deferred>
                    </Tile>

                    <Tile title="Pickup vs delivery" className={scoped}>
                        <Deferred data="mix" fallback={rows(2, 'h-7')}>
                            <SegmentedBar
                                label="Orders by fulfilment"
                                segments={(mix?.fulfilment ?? []).map((m) => ({
                                    key: m.key,
                                    label: m.label,
                                    value: m.orders,
                                    detail: `${money(m.revenue)} revenue`,
                                }))}
                                colorKeys={FULFILMENT_COLOURS}
                                unit={(v) => `${formatNumber(v)} ${v === 1 ? 'order' : 'orders'}`}
                                emptyText="No orders in this period yet."
                            />
                        </Deferred>
                    </Tile>

                    <Tile title="Low stock" aside={<LiveTag />}>
                        <Deferred data="low_stock" fallback={rows(4, 'h-9')}>
                            {(low_stock ?? []).length === 0 ? (
                                <p className="text-sm text-muted-foreground">Everything is above its minimum level.</p>
                            ) : (
                                <ul className="grid gap-3">
                                    {(low_stock ?? []).map((item) => {
                                        const out = item.stock <= 0;
                                        const fill = item.minimum > 0 ? Math.min(1, Math.max(0, item.stock / item.minimum)) : 0;
                                        return (
                                            <li key={`${item.kind}-${item.name}`} className="grid gap-1.5 text-sm">
                                                <div className="flex items-start justify-between gap-2">
                                                    <span className="grid min-w-0">
                                                        <span className="truncate font-medium">{item.name}</span>
                                                        <span className="text-xs text-muted-foreground">{item.kind}</span>
                                                    </span>
                                                    <span
                                                        className={cn(
                                                            'inline-flex shrink-0 items-center gap-1 text-xs font-medium',
                                                            out ? 'text-destructive-ink' : 'text-warning-ink',
                                                        )}
                                                    >
                                                        {out ? <PackageMinus className="size-3.5" /> : <AlertTriangle className="size-3.5" />}
                                                        {out ? 'Out' : 'Low'}
                                                    </span>
                                                </div>
                                                <div
                                                    className={cn('h-1.5 overflow-hidden rounded-full', out ? 'bg-destructive/15' : 'bg-warning/20')}
                                                    role="meter"
                                                    aria-valuemin={0}
                                                    aria-valuemax={item.minimum}
                                                    aria-valuenow={item.stock}
                                                    aria-label={`${item.name}: ${formatNumber(item.stock)} of ${formatNumber(item.minimum)} ${item.unit}`}
                                                >
                                                    <div
                                                        className={cn('h-full rounded-full', out ? 'bg-destructive' : 'bg-warning')}
                                                        style={{ width: `${fill * 100}%` }}
                                                    />
                                                </div>
                                                <span className="text-xs text-muted-foreground tabular-nums">
                                                    {formatNumber(item.stock)} of {formatNumber(item.minimum)} {item.unit} minimum
                                                </span>
                                            </li>
                                        );
                                    })}
                                </ul>
                            )}
                        </Deferred>
                    </Tile>

                    <Tile title="Latest orders" aside={<LiveTag />} className={cn('md:col-span-2', !canExport && 'xl:col-span-4')}>
                        <Deferred data="recent_orders" fallback={rows(5, 'h-10')}>
                            {(recent_orders ?? []).length === 0 ? (
                                <p className="text-sm text-muted-foreground">No orders yet.</p>
                            ) : (
                                <ul className="-mx-2 grid">
                                    {(recent_orders ?? []).map((order) => (
                                        <li key={order.id}>
                                            <Link
                                                href={editOrder(order.id).url}
                                                className="flex items-center justify-between gap-3 rounded-md px-2 py-2 text-sm hover:bg-accent focus-visible:bg-accent focus-visible:outline-none"
                                            >
                                                <span className="grid min-w-0">
                                                    <span className="truncate font-medium">{order.order_number}</span>
                                                    <span className="truncate text-xs text-muted-foreground">
                                                        {order.customer_name} · {formatDate(order.created_at, true)}
                                                    </span>
                                                </span>
                                                <span className="grid shrink-0 justify-items-end gap-1">
                                                    <span className="tabular-nums">{money(order.total)}</span>
                                                    <StatusBadge label={order.status} tone={toneFor(order.status)} />
                                                </span>
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </Deferred>
                    </Tile>

                    {canExport && (
                        <Tile title="Reports" className="md:col-span-2">
                            <ul className="-my-1 grid divide-y">
                                {reports.map((report) => (
                                    <li key={report.key} className="flex flex-col gap-2 py-2.5 sm:flex-row sm:items-center sm:justify-between">
                                        <span className="grid min-w-0">
                                            <span className="text-sm font-medium">{report.title}</span>
                                            <span className="text-xs text-muted-foreground">{report.description}</span>
                                        </span>
                                        <ReportDownloads type={report.key} />
                                    </li>
                                ))}
                            </ul>
                        </Tile>
                    )}
                </div>
            </div>
        </>
    );
}

Dashboard.layout = { breadcrumbs: [{ title: 'Home', href: dashboard().url }, { title: 'Dashboard' }] };

const REPORT_FORMATS = [
    { format: 'pdf', label: 'PDF', icon: FileText },
    { format: 'excel', label: 'Excel', icon: FileSpreadsheet },
    { format: 'word', label: 'Word', icon: FileText },
] as const;

/** PDF / Excel / Word buttons: download in the background, spinner while building, toast on failure. */
function ReportDownloads({ type }: { type: string }) {
    const [busy, setBusy] = useState<string | null>(null);

    const run = async (format: (typeof REPORT_FORMATS)[number]['format']) => {
        setBusy(format);
        try {
            await downloadFile(download({ type, format }).url, `ABCDips_${type}_report`);
        } catch (error) {
            toast.error(error instanceof Error ? error.message : 'The report could not be downloaded.');
        } finally {
            setBusy(null);
        }
    };

    return (
        <span className="flex shrink-0 gap-1.5">
            {REPORT_FORMATS.map(({ format, label, icon: Icon }) => (
                <Button key={format} variant="outline" size="sm" className="h-7 px-2 text-xs" disabled={busy !== null} onClick={() => run(format)}>
                    {busy === format ? <Loader2 className="size-3.5 animate-spin" /> : <Icon className="size-3.5" />} {label}
                </Button>
            ))}
        </span>
    );
}
