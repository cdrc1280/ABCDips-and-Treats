/** Mirrors App\Domains\Production\Resources\ProductionBatchResource (snake_case). */
export interface ProductionBatchRecord {
    id: string;
    batch_number: string | null;
    recipe_id: string;
    recipe_name: string | null;
    product_id: string;
    product_name: string | null;
    planned_qty: number;
    actual_yield_qty: number | null;
    status: string;
    status_label: string | null;
    baker_user_id: string | null;
    baker_name: string | null;
    notes: string | null;
    completed_at: string | null;
    is_completed: boolean;
    created_at: string | null;
    updated_at: string | null;
}
