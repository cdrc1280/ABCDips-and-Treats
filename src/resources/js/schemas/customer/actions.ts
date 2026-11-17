import { Pencil } from 'lucide-react';

import { CustomerPermission } from '@/lib/permissions';
import { edit } from '@/routes/admin/customers';
import type { CustomerRecord, PageAction, RowAction } from '@/types';

export const customerActionSchema: PageAction[] = [];

export function createCustomerActionSchema(): RowAction<CustomerRecord>[] {
    return [{ label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: CustomerPermission.CustomersEdit }];
}
