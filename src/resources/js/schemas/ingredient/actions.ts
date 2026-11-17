import { PackagePlus, Pencil, Plus, Trash2 } from 'lucide-react';

import { IngredientPermission } from '@/lib/permissions';
import { create, destroy, edit, restock } from '@/routes/admin/ingredients';
import type { IngredientRecord, PageAction, RowAction } from '@/types';

export const ingredientActionSchema: PageAction[] = [
    { label: 'New ingredient', icon: Plus, href: create().url, permission: IngredientPermission.IngredientsCreate },
];

export function createIngredientActionSchema(): RowAction<IngredientRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: IngredientPermission.IngredientsEdit },
        {
            label: 'Add stock',
            icon: PackagePlus,
            href: (row) => restock(row.id).url,
            method: 'patch',
            permission: IngredientPermission.IngredientsEdit,
            prompt: {
                field: 'add_qty',
                label: 'Quantity received',
                type: 'number',
                min: 0,
                step: '0.001',
                description: (row) => `Adds to the current ${row.stock_qty} ${row.unit} and logs a purchase movement.`,
            },
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: IngredientPermission.IngredientsDelete,
            hidden: (row) => row.used_in_recipes > 0,
            confirmTitle: 'Delete this ingredient?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
