import { ArrowRightCircle, CheckCircle2, FileSpreadsheet } from 'lucide-react';

import type { StatConfig } from '@/types';

export const quotationSummary: StatConfig = {
    open: { icon: FileSpreadsheet, tone: 'warning' },
    accepted: { icon: CheckCircle2, tone: 'success' },
    converted: { icon: ArrowRightCircle, tone: 'primary' },
};
