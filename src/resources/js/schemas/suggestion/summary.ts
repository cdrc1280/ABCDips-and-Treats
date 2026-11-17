import { CheckCircle2, Search, Sparkles } from 'lucide-react';

import type { StatConfig } from '@/types';

export const suggestionSummary: StatConfig = {
    new: { icon: Sparkles, tone: 'warning' },
    reviewing: { icon: Search, tone: 'primary' },
    implemented: { icon: CheckCircle2, tone: 'success' },
};
