<?php

namespace App\Domains\Content\Actions;

use App\Domains\Content\Models\BlogPost;
use Illuminate\Support\Facades\DB;

final class DeleteBlogPostAction
{
    public function execute(BlogPost $blogPost): void
    {
        DB::transaction(fn () => $blogPost->delete());
    }
}
