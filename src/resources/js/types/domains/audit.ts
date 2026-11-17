/** Mirrors App\Domains\Audit\Resources\ActivityResource (snake_case). */
export interface ActivityRecord {
    id: string;
    log_name: string | null;
    event: string | null;
    description: string;
    subject_type: string | null;
    subject_id: string | null;
    causer_name: string | null;
    changes: { field: string; old: unknown; new: unknown }[];
    created_at: string | null;
}
