<?php

namespace App\Domains\Content\Enums;

use App\Domains\Access\Contracts\DefinesPermissions;

enum BlogPostPermission: string implements DefinesPermissions
{
    case BlogPostsView = 'blog-posts-view';
    case BlogPostsCreate = 'blog-posts-create';
    case BlogPostsEdit = 'blog-posts-edit';
    case BlogPostsDelete = 'blog-posts-delete';

    public function group(): string
    {
        return 'Engagement & Content';
    }

    public function description(): string
    {
        return match ($this) {
            self::BlogPostsView => 'View blog posts',
            self::BlogPostsCreate => 'Create blog posts',
            self::BlogPostsEdit => 'Edit blog posts',
            self::BlogPostsDelete => 'Delete blog posts',
        };
    }
}
