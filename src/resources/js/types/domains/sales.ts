/** Mirrors App\Domains\Sales\Resources\CouponResource (snake_case). */
export interface CouponRecord {
    id: string;
    code: string;
    type: string;
    type_label: string | null;
    value: number;
    min_spend: number;
    max_discount: number | null;
    usage_limit: number | null;
    starts_at: string | null;
    expires_at: string | null;
    is_active: boolean;
    used_count: number;
    state: string;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Sales\Resources\CustomOrderResource (snake_case). */
export interface CustomOrderRecord {
    id: string;
    reference_number: string | null;
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    event_date: string | null;
    servings_count: number;
    tiers_count: number;
    flavor_preference: string | null;
    frosting_type: string | null;
    theme_description: string;
    budget_range_min: number | null;
    budget_range_max: number | null;
    quoted_price: number | null;
    status: string;
    status_label: string | null;
    staff_notes: string | null;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Sales\Resources\OrderResource (snake_case). */
export interface OrderRecord {
    id: string;
    order_number: string;
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    delivery_address: string | null;
    city: string | null;
    postal_code: string | null;
    scheduled_time: string | null;
    notes: string | null;
    payment_status: string;
    payment_status_label: string | null;
    payment_reference: string | null;
    status: string;
    status_label: string | null;
    /** Statuses the order may move to next (server-side rules). */
    next_statuses: string[];
    fulfillment_type: 'delivery' | 'pickup';
    delivery_mode: string;
    pooling_status: string;
    payment_method: string;
    subtotal: number;
    discount_amount: number;
    coupon_code: string | null;
    delivery_fee: number;
    total: number;
    paid_at: string | null;
    items_count: number;
    tracking_token: string;
    items: Array<{
        id: string;
        product_name: string;
        product_sku: string;
        qty: number;
        unit_price: number;
        subtotal: number;
        options: Record<string, unknown> | null;
    }>;
    history: Array<{
        id: string;
        from_status: string | null;
        to_status: string;
        comment: string | null;
        changed_by: string | null;
        created_at: string | null;
    }>;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Sales\Resources\DeliveryPoolResource (snake_case). */
export interface DeliveryPoolRecord {
    id: string;
    pool_code: string | null;
    zone_name: string | null;
    region: string | null;
    province: string | null;
    city: string | null;
    barangay: string | null;
    total_delivery_fee: number;
    status: string;
    status_label: string | null;
    allocations: Array<{ order_id: string | null; assigned_fee: number | null }>;
    notes: string | null;
    orders_count: number;
    allocated_fees: number;
    shared_fee_per_order: number;
    settled_at: string | null;
    is_locked: boolean;
    created_at: string | null;
    updated_at: string | null;
}
