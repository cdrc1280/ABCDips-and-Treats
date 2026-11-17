/** Mirrors App\Domains\Costing\Resources\RecipeResource (snake_case). */
export interface RecipeRecord {
    id: string;
    name: string;
    product_id: string;
    product_name: string | null;
    yield_qty: number;
    prep_time_minutes: number;
    baking_time_minutes: number;
    instructions: string | null;
    ingredients: Array<{
        id: string;
        ingredient_id: string | null;
        qty_required: number | null;
        unit: string | null;
        line_cost: number;
        ingredient_name: string | null;
    }>;
    packaging: Array<{
        id: string;
        packaging_material_id: string | null;
        name: string | null;
        unit: string | null;
        package_qty: number | null;
        package_cost: number | null;
        qty_used: number | null;
    }>;
    overhead_pct: number;
    markup_pct: number;
    labor_pct: number;
    ingredient_cost: number;
    packaging_cost: number;
    total_cost: number;
    unit_cost: number;
    unit_selling_price: number;
    gross_margin: number;
    product_price: number | null;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Costing\Resources\ProductCostingResource (snake_case). */
export interface ProductCostingRecord {
    id: string;
    name: string | null;
    product_id: string | null;
    product_name: string | null;
    yield_qty: number;
    yield_unit: string;
    items: Array<{
        id: string;
        group: string | null;
        ingredient_id: string | null;
        packaging_material_id: string | null;
        name: string | null;
        unit: string | null;
        package_amount: number | null;
        package_price: number | null;
        qty_used: number | null;
        line_cost: number;
    }>;
    overhead_pct: number;
    markup_pct: number;
    labor_pct: number;
    raw_cost: number;
    total_cost: number;
    selling_price: number;
    price_per_piece: number;
    created_at: string | null;
    updated_at: string | null;
}
