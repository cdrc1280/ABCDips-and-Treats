import { EyeOff, MessageSquare, Sparkles, Star } from 'lucide-react';

import type { StatConfig } from '@/types';

export const reviewSummary: StatConfig = {
    total: { icon: MessageSquare, tone: 'primary' },
    average: { icon: Star, tone: 'warning' },
    pending: { icon: EyeOff, tone: 'muted' },
    featured: { icon: Sparkles, tone: 'success' },
};
