/** Mirrors App\Domains\Content\Resources\BlogPostResource (snake_case). */
export interface BlogPostRecord {
    id: string;
    title: string;
    slug: string;
    category: string;
    excerpt: string | null;
    body: string;
    cover_image: string | null;
    cover_image_url: string | null;
    status: string;
    status_label: string | null;
    published_at: string | null;
    seo_title: string | null;
    seo_description: string | null;
    author_name: string | null;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Content\Resources\ContactMessageResource (snake_case). */
export interface ContactMessageRecord {
    id: string;
    name: string;
    email: string;
    phone: string | null;
    subject: string;
    message: string;
    status: string;
    status_label: string | null;
    admin_notes: string | null;
    replied_at: string | null;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Content\Resources\SuggestionResource (snake_case). */
export interface SuggestionRecord {
    id: string;
    name: string;
    email: string;
    category: string;
    category_label: string | null;
    subject: string;
    message: string;
    status: string;
    status_label: string | null;
    admin_notes: string | null;
    created_at: string | null;
    updated_at: string | null;
}

/** Mirrors App\Domains\Content\Resources\ChatEscalationResource (snake_case). */
export interface ChatEscalationRecord {
    id: string;
    guest_name: string | null;
    guest_email: string | null;
    status: string;
    status_label: string | null;
    admin_notes: string | null;
    customer_name: string;
    messages: Array<{ role: 'user' | 'assistant' | 'admin'; content: string; time: string | null }>;
    messages_count: number;
    last_message: string | null;
    created_at: string | null;
    updated_at: string | null;
}
