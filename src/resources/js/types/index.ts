import type { LucideIcon } from 'lucide-react';
import type { CartSummary, StoreInfo } from './domains/storefront';

export type * from './data-table';
export type * from './filter';
export type * from './actions';
export type * from './domains/access';
export type * from './domains/catalog';
export type * from './domains/audit';
export type * from './domains/user';
export type * from './domains/purchasing';
export type * from './domains/costing';
export type * from './domains/production';
export type * from './domains/content';
export type * from './domains/review';
export type * from './domains/employee';
export type * from './domains/sales';
export type * from './domains/inventory';
export type * from './domains/storefront';

export interface AuthUser {
    id: string;
    name: string;
    email: string;
    phone: string | null;
    avatar_url: string | null;
    email_verified: boolean;
}

export interface Auth {
    user: AuthUser | null;
    roles: string[];
    permissions: string[];
    is_super_admin: boolean;
    can_access_admin?: boolean;
}

export interface MenuItem {
    title: string;
    icon: string | null;
    href: string | null;
    route_name: string | null;
    items: MenuItem[];
}

export interface BreadcrumbItem {
    title: string;
    href?: string;
}

export interface Toast {
    type: 'success' | 'error' | 'info' | 'warning';
    message: string;
}

export interface SharedData {
    app: { name: string; area: 'admin' | 'storefront' };
    auth: Auth;
    menus: MenuItem[];
    sidebarOpen: boolean;
    notifications: { unread: number } | null;
    form_rules: { required: string[] };
    errors: Record<string, string>;
    cart: CartSummary | null;
    store: StoreInfo | null;
    wishlist_ids: string[];
    [key: string]: unknown;
}

/** One stat card — mirrors App\Shared\Resources\StatData. */
export interface StatData {
    key: string;
    label: string;
    value: number;
    description: string | null;
    display: string | null;
}

export interface StatConfigEntry {
    icon: LucideIcon;
    tone?: 'primary' | 'success' | 'warning' | 'muted' | 'destructive';
}
export type StatConfig = Record<string, StatConfigEntry>;

export interface Option<V = string | number> {
    label: string;
    value: V;
    description?: string | null;
}

/** spatie/laravel-data PaginatedDataCollection output. */
export interface Paginated<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        per_page: number;
        to: number | null;
        total: number;
        path: string;
    };
}

export interface PageLayoutMeta {
    breadcrumbs?: BreadcrumbItem[];
}

/** Flashed after a POS sale (PosController::checkout). */
export interface PosReceipt {
    id: string;
    order_number: string;
    total: number;
    payment_method: string;
    cash_tendered: number | null;
    change_due: number;
    items: { name: string; qty: number; subtotal: number }[];
}

/** Flashed by AiAdvisorController::ask. */
export interface AdvisorAnswer {
    id: string;
    question: string;
    answer: string;
    source: string;
}
