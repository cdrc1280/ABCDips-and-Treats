import { ChefHat, Clock, ShoppingBag, Wallet } from 'lucide-react';

import type { StatConfig } from '@/types';

export const orderSummary: StatConfig = {
    today: { icon: ShoppingBag, tone: 'primary' },
    open: { icon: ChefHat, tone: 'warning' },
    awaiting: { icon: Clock, tone: 'muted' },
    revenue_today: { icon: Wallet, tone: 'success' },
};
