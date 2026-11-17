import { BadgeCheck, Banknote, PenLine } from 'lucide-react';

import type { StatConfig } from '@/types';

export const payrollSummary: StatConfig = {
    draft: { icon: PenLine, tone: 'warning' },
    approved: { icon: BadgeCheck, tone: 'primary' },
    paid_ytd: { icon: Banknote, tone: 'success' },
};
