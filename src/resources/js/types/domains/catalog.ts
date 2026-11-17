/** Mirrors App\Domains\Catalog\Resources\CategoryResource (snake_case). */
export interface CategoryRecord {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    sort_order: number;
    is_active: boolean;
    image_path: string | null;
    image_url: string | null;
    products_count: number;
    has_products: boolean;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Catalog\Resources\ProductResource (snake_case). */
export interface ProductRecord {
    id: string;
    name: string;
    slug: string;
    category_id: string;
    category_name: string | null;
    sku: string;
    barcode: string | null;
    short_description: string | null;
    description: string | null;
    tags: string[];
    image_path: string | null;
    image_path_url: string | null;
    gallery: string[];
    gallery_urls: string[];
    price: number;
    sale_price: number | null;
    sale_ends_at: string | null;
    stock_qty: number;
    min_stock_qty: number;
    inventory_mode: 'made_to_order' | 'baked_ahead';
    prep_time_minutes: number;
    variation_type: string;
    variation_type_label: string | null;
    variations: Array<{ label: string | null; price_modifier: number | null }>;
    is_assorted: boolean;
    max_flavors: number | null;
    flavor: string | null;
    flavors: Array<{ name: string | null; price_modifier: number | null }>;
    is_featured: boolean;
    is_best_seller: boolean;
    is_new_arrival: boolean;
    is_seasonal: boolean;
    is_limited: boolean;
    is_active: boolean;
    seo_title: string | null;
    seo_description: string | null;
    is_low_stock: boolean;
    is_on_sale: boolean;
    has_orders: boolean;
    created_at: string | null;
    updated_at: string | null;
}
