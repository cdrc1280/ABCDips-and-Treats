<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Data\BlogPostData;
use App\Domains\Content\Models\BlogPost;
use App\Shared\Services\PublicUploads;
use Illuminate\Support\Facades\DB;

final class CreateBlogPostAction
{
    public function __construct(private readonly PublicUploads $uploads) {}

    public function execute(BlogPostData $data): BlogPost
    {
        return DB::transaction(function () use ($data) {
            $blogPost = BlogPost::query()->create([
                'title' => $data->title,
                'slug' => $data->slug,
                'category' => $data->category,
                'excerpt' => $data->excerpt,
                'body' => $data->body,
                'cover_image' => $data->coverImage ? $this->uploads->store($data->coverImage, 'blog-posts') : null,
                'status' => $data->status,
                'published_at' => $data->publishedAt,
                'seo_title' => $data->seoTitle,
                'seo_description' => $data->seoDescription,
                'author_id' => auth()->id(),
            ]);

            return $blogPost;
        });
    }
}
