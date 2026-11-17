/** Mirrors App\Domains\Purchasing\Resources\QuotationResource (snake_case). */
export interface QuotationRecord {
    id: string;
    quotation_number: string | null;
    supplier_id: string | null;
    supplier_name: string | null;
    client_name: string | null;
    status: string;
    status_label: string | null;
    quotation_date: string | null;
    valid_until: string | null;
    items: Array<{
        id: string;
        ingredient_id: string | null;
        item_description: string | null;
        qty: number | null;
        unit: string | null;
        unit_price: number | null;
    }>;
    tax: number;
    notes: string | null;
    subtotal: number;
    total: number;
    is_converted: boolean;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Purchasing\Resources\PurchaseOrderResource (snake_case). */
export interface PurchaseOrderRecord {
    id: string;
    po_number: string | null;
    supplier_id: string;
    supplier_name: string | null;
    status: string;
    status_label: string | null;
    expected_delivery_date: string | null;
    payment_terms: string | null;
    items: Array<{ id: string; ingredient_id: string | null; qty_ordered: number | null; unit_cost: number | null }>;
    tax: number;
    delivery_receipt_no: string | null;
    sales_invoice_no: string | null;
    notes: string | null;
    subtotal: number;
    total: number;
    received_at: string | null;
    is_closed: boolean;
    created_at: string | null;
    updated_at: string | null;
}
