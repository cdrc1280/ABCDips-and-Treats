import { Inbox, MailCheck, MailWarning } from 'lucide-react';

import type { StatConfig } from '@/types';

export const contactMessageSummary: StatConfig = {
    unread: { icon: MailWarning, tone: 'warning' },
    replied: { icon: MailCheck, tone: 'success' },
    week: { icon: Inbox, tone: 'primary' },
};
