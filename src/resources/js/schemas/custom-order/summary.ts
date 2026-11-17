import { Cake, CalendarClock, CheckCircle2, MessageCircleQuestion } from 'lucide-react';

import type { StatConfig } from '@/types';

export const customOrderSummary: StatConfig = {
    open: { icon: MessageCircleQuestion, tone: 'warning' },
    upcoming: { icon: CalendarClock, tone: 'primary' },
    in_production: { icon: Cake, tone: 'muted' },
    completed: { icon: CheckCircle2, tone: 'success' },
};
