import { BadgeCheck, Pencil, Plus, Trash2 } from 'lucide-react';

import { RecipePermission } from '@/lib/permissions';
import { applyPrice, create, destroy, edit } from '@/routes/admin/recipes';
import type { RecipeRecord, PageAction, RowAction } from '@/types';

export const recipeActionSchema: PageAction[] = [{ label: 'New recipe', icon: Plus, href: create().url, permission: RecipePermission.RecipesCreate }];

export function createRecipeActionSchema(): RowAction<RecipeRecord>[] {
    return [
        { label: 'Edit', icon: Pencil, href: (row) => edit(row.id).url, permission: RecipePermission.RecipesEdit },
        {
            label: 'Use suggested price',
            icon: BadgeCheck,
            href: (row) => applyPrice(row.id).url,
            method: 'patch',
            permission: RecipePermission.RecipesEdit,
            isWithConfirmation: true,
            confirmTitle: 'Update the product price?',
            confirmDescription: 'Sets the product’s selling price to this recipe’s suggested unit price.',
        },
        {
            label: 'Delete',
            icon: Trash2,
            href: (row) => destroy(row.id).url,
            method: 'delete',
            destructive: true,
            isWithConfirmation: true,
            permission: RecipePermission.RecipesDelete,
            confirmTitle: 'Delete this recipe?',
            confirmDescription: (row) => `"${row.name}" will be removed. This can't be undone from the back office.`,
        },
    ];
}
