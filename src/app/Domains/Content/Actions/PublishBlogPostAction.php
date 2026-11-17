<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\BlogPost;
use Illuminate\Support\Facades\DB;

/** Publish now. */
final class PublishBlogPostAction
{
    public function execute(BlogPost $blogPost): BlogPost
    {
        DB::transaction(function () use ($blogPost) {
            $blogPost->update(['status' => 'published', 'published_at' => $blogPost->published_at ?? now()]);
        });

        return $blogPost->refresh();
    }
}
