import { CheckCircle2, Hourglass, Route } from 'lucide-react';

import type { StatConfig } from '@/types';

export const deliveryPoolSummary: StatConfig = {
    waiting: { icon: Hourglass, tone: 'warning' },
    open: { icon: Route, tone: 'primary' },
    settled: { icon: CheckCircle2, tone: 'success' },
};
