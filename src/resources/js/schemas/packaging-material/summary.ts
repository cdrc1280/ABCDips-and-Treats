import { AlertTriangle, PackageOpen, Wallet } from 'lucide-react';

import type { StatConfig } from '@/types';

export const packagingMaterialSummary: StatConfig = {
    total: { icon: PackageOpen, tone: 'primary' },
    low: { icon: AlertTriangle, tone: 'destructive' },
    value: { icon: Wallet, tone: 'success' },
};
