/** Mirrors App\Domains\User\Resources\UserResource (snake_case). */
export interface UserRecord {
    id: string;
    name: string;
    email: string;
    phone: string | null;
    roles: string[];
    email_verified: boolean;
    roles_label: string;
    is_self: boolean;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\User\Resources\CustomerResource (snake_case). */
export interface CustomerRecord {
    id: string;
    name: string;
    email: string;
    phone: string | null;
    street_address: string | null;
    barangay: string | null;
    city: string | null;
    province: string | null;
    region: string | null;
    orders_count: number;
    total_spent: number;
    email_verified: boolean;
    created_at: string | null;
    updated_at: string | null;
}
