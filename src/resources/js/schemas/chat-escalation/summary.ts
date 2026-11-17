import { CheckCircle2, MessageCircleWarning, MessagesSquare } from 'lucide-react';

import type { StatConfig } from '@/types';

export const chatEscalationSummary: StatConfig = {
    open: { icon: MessageCircleWarning, tone: 'warning' },
    in_progress: { icon: MessagesSquare, tone: 'primary' },
    resolved: { icon: CheckCircle2, tone: 'success' },
};
