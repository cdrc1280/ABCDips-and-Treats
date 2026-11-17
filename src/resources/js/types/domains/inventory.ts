/** Mirrors App\Domains\Inventory\Resources\SupplierResource (snake_case). */
export interface SupplierRecord {
    id: string;
    name: string;
    contact_person: string;
    email: string;
    phone: string;
    payment_terms: string;
    address: string;
    notes: string | null;
    ingredients_count: number;
    purchase_orders_count: number;
    in_use: boolean;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Inventory\Resources\IngredientResource (snake_case). */
export interface IngredientRecord {
    id: string;
    name: string;
    sku: string;
    supplier_id: string | null;
    supplier_name: string | null;
    unit: string;
    unit_label: string | null;
    item_unit: number;
    item_price: number;
    stock_qty: number;
    min_stock_qty: number;
    reorder_qty: number;
    cost_per_unit: number;
    is_low_stock: boolean;
    used_in_recipes: number;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Inventory\Resources\PackagingMaterialResource (snake_case). */
export interface PackagingMaterialRecord {
    id: string;
    name: string;
    sku: string;
    type: string;
    type_label: string | null;
    unit: string;
    cost_per_unit: number;
    stock_qty: number;
    min_stock_qty: number;
    is_active: boolean;
    notes: string | null;
    is_low_stock: boolean;
    created_at: string | null;
    updated_at: string | null;
}
