<?php

namespace App\Domains\Content\Resources;

use App\Domains\Content\Models\BlogPost;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

#[MapName(SnakeCaseMapper::class)]
final class BlogPostResource extends Resource
{
    public function __construct(
        public string $id,
        public string $title,
        public string $slug,
        public string $category,
        public ?string $excerpt,
        public string $body,
        public ?string $coverImage,
        public ?string $coverImageUrl,
        public string $status,
        public ?string $statusLabel,
        public ?string $publishedAt,
        public ?string $seoTitle,
        public ?string $seoDescription,
        public ?string $authorName,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public static function fromModel(BlogPost $blogPost, array $context = []): self
    {
        return new self(
            id: $blogPost->id,
            title: $blogPost->title,
            slug: $blogPost->slug,
            category: $blogPost->category,
            excerpt: $blogPost->excerpt,
            body: $blogPost->body,
            coverImage: $blogPost->cover_image,
            coverImageUrl: self::url($blogPost->cover_image),
            status: (string) $blogPost->status,
            statusLabel: self::option(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'], $blogPost->status),
            publishedAt: self::dateTime($blogPost->published_at),
            seoTitle: $blogPost->seo_title,
            seoDescription: $blogPost->seo_description,
            authorName: $blogPost->author?->name,
            createdAt: $blogPost->created_at?->toIso8601String(),
            updatedAt: $blogPost->updated_at?->toIso8601String(),
        );
    }

    private static function dateTime(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return ($value instanceof \DateTimeInterface ? Carbon::instance($value) : Carbon::parse((string) $value))->toIso8601String();
    }

    private static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : asset('storage/'.ltrim($path, '/'));
    }

    /**
     * @param  array<string, string>  $options
     */
    private static function option(array $options, mixed $value): ?string
    {
        return $value === null ? null : ($options[(string) $value] ?? (string) $value);
    }
}
