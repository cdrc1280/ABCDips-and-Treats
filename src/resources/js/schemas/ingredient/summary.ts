import { AlertTriangle, Wallet, Wheat } from 'lucide-react';

import type { StatConfig } from '@/types';

export const ingredientSummary: StatConfig = {
    total: { icon: Wheat, tone: 'primary' },
    low: { icon: AlertTriangle, tone: 'destructive' },
    value: { icon: Wallet, tone: 'success' },
};
