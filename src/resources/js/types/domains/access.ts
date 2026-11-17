/** Mirrors App\Domains\Access\Resources\RoleResource (snake_case). */
export interface RoleRecord {
    id: string;
    name: string;
    permissions: string[];
    users_count: number;
    permissions_count: number;
    is_system: boolean;
    created_at: string | null;
    updated_at: string | null;
}

export interface PermissionGroup {
    group: string;
    permissions: { name: string; description: string }[];
}
