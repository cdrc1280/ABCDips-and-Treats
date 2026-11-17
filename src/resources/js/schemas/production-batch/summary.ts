import { CalendarClock, CheckCircle2, Flame } from 'lucide-react';

import type { StatConfig } from '@/types';

export const productionBatchSummary: StatConfig = {
    planned: { icon: CalendarClock, tone: 'muted' },
    active: { icon: Flame, tone: 'warning' },
    completed_week: { icon: CheckCircle2, tone: 'success' },
};
