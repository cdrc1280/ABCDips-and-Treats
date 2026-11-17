<?php

namespace App\Domains\Content\Data;

use App\Shared\Support\RouteModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class BlogPostData extends Data
{
    public function __construct(
        public string $title,
        public string $slug,
        public string $body,
        public ?string $category = 'General',
        public ?string $excerpt = null,
        public ?UploadedFile $coverImage = null,
        public bool $removeCoverImage = false,
        public ?string $status = 'draft',
        public ?string $publishedAt = null,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        $properties['title'] = trim((string) ($properties['title'] ?? '')) ?: null;
        $properties['slug'] = Str::slug(trim((string) ($properties['slug'] ?? '')) ?: (string) ($properties['title'] ?? ''));
        $properties['body'] = trim((string) ($properties['body'] ?? '')) ?: null;
        $properties['category'] = trim((string) ($properties['category'] ?? '')) ?: 'General';
        $properties['excerpt'] = trim((string) ($properties['excerpt'] ?? '')) ?: null;
        $properties['remove_cover_image'] = filter_var($properties['remove_cover_image'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $properties['status'] = trim((string) ($properties['status'] ?? '')) ?: 'draft';
        $properties['published_at'] = trim((string) ($properties['published_at'] ?? '')) ?: null;
        $properties['seo_title'] = trim((string) ($properties['seo_title'] ?? '')) ?: null;
        $properties['seo_description'] = trim((string) ($properties['seo_description'] ?? '')) ?: null;

        return $properties;
    }

    public static function rules(?ValidationContext $context = null): array
    {
        $id = RouteModel::key('blogPost');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_posts', 'slug')->ignore($id)->whereNull('deleted_at')],
            'body' => ['required', 'string', 'max:100000'],
            'category' => ['required', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:600'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_cover_image' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    public static function messages(mixed ...$args): array
    {
        return [

        ];
    }

    public static function attributes(mixed ...$args): array
    {
        return [
            'title' => __('title'),
            'slug' => __('url slug'),
            'category' => __('category'),
            'excerpt' => __('excerpt'),
            'body' => __('body'),
            'status' => __('status'),
            'published_at' => __('publish date'),
            'seo_title' => __('seo title'),
            'seo_description' => __('seo description'),
        ];
    }
}
