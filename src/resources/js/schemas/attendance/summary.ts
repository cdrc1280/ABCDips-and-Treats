import { Clock, Timer, UserCheck } from 'lucide-react';

import type { StatConfig } from '@/types';

export const attendanceSummary: StatConfig = {
    today: { icon: UserCheck, tone: 'success' },
    late: { icon: Clock, tone: 'warning' },
    overtime: { icon: Timer, tone: 'muted' },
};
