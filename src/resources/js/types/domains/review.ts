/** Mirrors App\Domains\Review\Resources\ReviewResource (snake_case). */
export interface ReviewRecord {
    id: string;
    product_id: string | null;
    product_name: string | null;
    rating: number;
    rating_label: string | null;
    reviewer_name: string;
    reviewer_email: string;
    title: string | null;
    comment: string;
    is_approved: boolean;
    is_featured: boolean;
    is_verified_buyer: boolean;
    is_anonymous: boolean;
    helpful_votes: number;
    created_at: string | null;
    updated_at: string | null;
}
