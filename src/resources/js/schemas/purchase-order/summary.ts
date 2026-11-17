import { ClipboardList, PackageCheck, Wallet } from 'lucide-react';

import type { StatConfig } from '@/types';

export const purchaseOrderSummary: StatConfig = {
    open: { icon: ClipboardList, tone: 'warning' },
    open_value: { icon: Wallet, tone: 'primary' },
    received_month: { icon: PackageCheck, tone: 'success' },
};
