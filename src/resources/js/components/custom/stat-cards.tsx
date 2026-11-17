import { CircleDot } from 'lucide-react';

import { Skeleton } from '@/components/ui/skeleton';
import { formatNumber } from '@/lib/format';
import { cn } from '@/lib/utils';
import type { StatConfig, StatData } from '@/types';

const tones = {
    primary: 'bg-primary/10 text-primary',
    success: 'bg-success/12 text-success-ink',
    warning: 'bg-warning/14 text-warning-ink',
    muted: 'bg-muted text-muted-foreground',
    destructive: 'bg-destructive/10 text-destructive-ink',
} as const;

/** One horizontal strip of stat cards (scrolls sideways, never wraps). `stats` is deferred → undefined first. */
export function StatCards({ stats, config, count = 4 }: { stats?: StatData[]; config: StatConfig; count?: number }) {
    return (
        <div className="flex gap-3 overflow-x-auto pb-1">
            {stats === undefined
                ? Array.from({ length: count }).map((_, i) => (
                      <div key={i} className="flex min-w-40 flex-1 items-center gap-3 rounded-xl border bg-card p-3 shadow-xs sm:min-w-56 sm:p-4">
                          <Skeleton className="hidden size-9 rounded-md sm:block" />
                          <div className="grid flex-1 gap-2">
                              <Skeleton className="h-5 w-16" />
                              <Skeleton className="h-3 w-24" />
                          </div>
                      </div>
                  ))
                : stats.map((stat) => {
                      const entry = config[stat.key];
                      const Icon = entry?.icon ?? CircleDot;
                      return (
                          <div
                              key={stat.key}
                              className="flex min-w-40 flex-1 items-center gap-3 rounded-xl border bg-card p-3 shadow-xs sm:min-w-56 sm:p-4"
                          >
                              <span
                                  className={cn(
                                      'hidden size-9 shrink-0 items-center justify-center rounded-md sm:flex',
                                      tones[entry?.tone ?? 'primary'],
                                  )}
                              >
                                  <Icon className="size-4" />
                              </span>
                              <div className="min-w-0">
                                  <div className="text-lg font-semibold tabular-nums sm:text-xl">{stat.display ?? formatNumber(stat.value)}</div>
                                  <div className="truncate text-sm font-medium">{stat.label}</div>
                                  {stat.description && (
                                      <div className="hidden truncate text-xs text-muted-foreground sm:block">{stat.description}</div>
                                  )}
                              </div>
                          </div>
                      );
                  })}
        </div>
    );
}
