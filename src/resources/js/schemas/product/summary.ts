import { AlertTriangle, BadgePercent, Croissant, Eye } from 'lucide-react';

import type { StatConfig } from '@/types';

export const productSummary: StatConfig = {
    total: { icon: Croissant, tone: 'primary' },
    active: { icon: Eye, tone: 'success' },
    low: { icon: AlertTriangle, tone: 'destructive' },
    on_sale: { icon: BadgePercent, tone: 'warning' },
};
