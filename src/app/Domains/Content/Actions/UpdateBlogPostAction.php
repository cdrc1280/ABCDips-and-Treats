<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Data\BlogPostData;
use App\Domains\Content\Models\BlogPost;
use App\Shared\Services\PublicUploads;
use Illuminate\Support\Facades\DB;

final class UpdateBlogPostAction
{
    public function __construct(private readonly PublicUploads $uploads) {}

    public function execute(BlogPost $blogPost, BlogPostData $data): BlogPost
    {
        return DB::transaction(function () use ($blogPost, $data) {
            $blogPost->update([
                'title' => $data->title,
                'slug' => $data->slug,
                'category' => $data->category,
                'excerpt' => $data->excerpt,
                'body' => $data->body,
                'cover_image' => $this->uploads->replace($blogPost->cover_image, $data->coverImage, 'blog-posts', $data->removeCoverImage),
                'status' => $data->status,
                'published_at' => $data->publishedAt,
                'seo_title' => $data->seoTitle,
                'seo_description' => $data->seoDescription,
            ]);

            return $blogPost;
        });
    }
}
