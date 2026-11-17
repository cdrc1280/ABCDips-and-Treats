/** Mirrors App\Domains\Storefront\Support\CurrentCart::present(). */
export interface CartLineOptions {
    variation?: string;
    flavor?: string;
    flavors?: string[];
    is_custom?: boolean;
    custom_title?: string;
    flavor_preference?: string;
    frosting_type?: string;
    budget_range_min?: number;
    budget_range_max?: number;
    cake_inscription?: string;
    event_date?: string;
    [key: string]: unknown;
}

export interface CartLine {
    id: string;
    product_id: string;
    name: string | null;
    slug: string | null;
    image_url: string | null;
    qty: number;
    unit_price: number;
    subtotal: number;
    options: CartLineOptions | null;
}

export interface CartSummary {
    items: CartLine[];
    item_count: number;
    subtotal: number;
    discount: number;
    fees: number;
    total: number;
    coupon_code: string | null;
}

export interface StoreInfo {
    name: string;
    phone: string;
    email: string;
    address: string;
}

/** Mirrors App\Domains\Storefront\Resources\ProductPresenter::card(). */
export interface ProductCardData {
    id: string;
    name: string;
    slug: string;
    short_description: string | null;
    price: number;
    effective_price: number;
    is_on_sale: boolean;
    sale_ends_at: string | null;
    prep_time_minutes: number | null;
    /** Real photo, or null: render <ProductPhoto> for the designed fallback. */
    image_url: string | null;
    category: { name: string; slug: string } | null;
    stock_qty: number;
    is_in_stock: boolean;
    is_low_stock: boolean;
    is_featured: boolean;
    is_best_seller: boolean;
    is_new_arrival: boolean;
    is_limited: boolean;
    is_seasonal: boolean;
    has_options: boolean;
    avg_rating: number | null;
    reviews_count: number;
}

export interface ProductVariation {
    label: string;
    price_modifier: number;
}

export interface ProductFlavor {
    name: string;
    price_modifier: number;
}

/** Mirrors ProductPresenter::detail(). */
export interface ProductDetailData extends ProductCardData {
    sku: string;
    description: string | null;
    flavor: string | null;
    variation_type: string;
    variations: ProductVariation[];
    flavors: ProductFlavor[];
    is_assorted: boolean;
    /** Has an opt-in 3D model ("Turn the loaf"), see Product::SLUGS_WITH_3D_MODEL. */
    has_3d: boolean;
    max_flavors: number | null;
    images: string[];
    tags: string[];
    allergens: { name: string; type: string | null }[];
    nutrition: {
        serving_size: string | null;
        calories: number | null;
        fat_g: number;
        carbs_g: number;
        protein_g: number;
        sodium_mg: number;
        sugar_g: number;
    } | null;
    seo: { title: string; description: string | null };
}

export interface StorefrontCategory {
    name: string;
    slug: string;
    description: string | null;
    image_url: string | null;
    products_count: number;
}

/** Mirrors App\Http\Resources\ReviewResource. */
export interface StorefrontReview {
    id: string;
    product_id: string | null;
    reviewer_name: string;
    rating: number;
    title: string | null;
    comment: string;
    is_anonymous: boolean;
    is_verified_buyer: boolean;
    is_featured: boolean;
    helpful_votes: number;
    photos: string[];
    created_at: string;
}

/** Mirrors ReviewService::getStoreServiceReviews(). */
export interface StoreReviewsData {
    stats: {
        total_reviews: number;
        avg_rating: number | null;
        distribution: Record<'1' | '2' | '3' | '4' | '5', number>;
    };
    reviews: { data: StorefrontReview[] };
}

/** Mirrors App\Domains\Storefront\Resources\OrderPresenter. */
export interface CustomerOrderSummary {
    id: string;
    order_number: string;
    status: string;
    status_label: string;
    payment_method: string | null;
    payment_status: string | null;
    fulfillment_type: string | null;
    total: number;
    items_count: number | null;
    created_at: string | null;
    can_cancel: boolean;
}

export interface CustomerOrderDetail extends CustomerOrderSummary {
    tracking_token: string;
    customer_name: string;
    customer_email: string;
    customer_phone: string | null;
    delivery_mode: string;
    pooling_status: string;
    delivery_address: string | null;
    city: string | null;
    scheduled_time: string | null;
    notes: string | null;
    subtotal: number;
    discount_amount: number;
    coupon_code: string | null;
    delivery_fee: number;
    estimated_shared_fee: number;
    payment_reference: string | null;
    invoice_available: boolean;
    items: {
        id: string;
        product_name: string;
        product_slug: string | null;
        image_url: string | null;
        qty: number;
        unit_price: number;
        subtotal: number;
        options: CartLineOptions | null;
    }[];
    timeline: { status: string; label: string; comment: string | null; created_at: string | null }[];
}

export interface BlogPostCard {
    id: string;
    title: string;
    slug: string;
    excerpt: string | null;
    category: string | null;
    cover_url: string | null;
    author: string | null;
    published_at: string | null;
    reading_minutes: number;
}
