<?php

namespace App\Domains\Content\Controllers;

use App\Domains\Content\Actions\CreateBlogPostAction;
use App\Domains\Content\Actions\DeleteBlogPostAction;
use App\Domains\Content\Actions\PublishBlogPostAction;
use App\Domains\Content\Actions\UpdateBlogPostAction;
use App\Domains\Content\Data\BlogPostData;
use App\Domains\Content\Enums\BlogPostPermission;
use App\Domains\Content\Models\BlogPost;
use App\Domains\Content\Queries\BlogPostQueries;
use App\Domains\Content\Resources\BlogPostResource;
use App\Domains\Preference\Actions\ResolveTableStateAction;
use App\Http\Controllers\Controller;
use App\Shared\Attributes\FormRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelData\PaginatedDataCollection;

class BlogPostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:'.BlogPostPermission::BlogPostsView->value, only: ['index']),
            new Middleware('can:'.BlogPostPermission::BlogPostsCreate->value, only: ['create', 'store']),
            new Middleware('can:'.BlogPostPermission::BlogPostsEdit->value, only: ['edit', 'update', 'publish']),
            new Middleware('can:'.BlogPostPermission::BlogPostsDelete->value, only: ['destroy']),
        ];
    }

    public function index(Request $request, ResolveTableStateAction $resolveTableState, BlogPostQueries $queries): Response
    {
        $table = $resolveTableState->execute($request, $request->user(), 'blog_posts', ['sort_field' => 'created_at', 'sort_dir' => 'desc']);
        $paginator = $queries->paginate($table->query);
        $paginator->getCollection()->transform(fn (BlogPost $blogPost) => BlogPostResource::fromModel($blogPost));

        return Inertia::render('blog-post/index', [
            'records' => BlogPostResource::collect($paginator, PaginatedDataCollection::class),
            'filters' => $table->query,
            'table' => $table->forFrontend(),
        ]);
    }

    #[FormRules(BlogPostData::class)]
    public function create(): Response
    {
        return Inertia::render('blog-post/create', [
        ]);
    }

    public function store(BlogPostData $data, CreateBlogPostAction $create): RedirectResponse
    {
        $blogPost = $create->execute($data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been created successfully.', ['name' => $blogPost->title])]);

        return to_route('admin.blog-posts.index');
    }

    #[FormRules(BlogPostData::class)]
    public function edit(BlogPost $blogPost, BlogPostQueries $queries): Response
    {
        return Inertia::render('blog-post/edit', [
            'record' => BlogPostResource::fromModel($blogPost),
            'switcher' => Inertia::defer(fn () => $queries->getAllAsOptions(), 'switcher'),
        ]);
    }

    public function update(BlogPost $blogPost, BlogPostData $data, UpdateBlogPostAction $update): RedirectResponse
    {
        $update->execute($blogPost, $data);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been updated successfully.', ['name' => $blogPost->title])]);

        return to_route('admin.blog-posts.index');
    }

    /** Publish now. */
    public function publish(BlogPost $blogPost, PublishBlogPostAction $action): RedirectResponse
    {
        $action->execute($blogPost);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':title is now live on the blog.', ['title' => $blogPost->title])]);

        return back();
    }

    public function destroy(BlogPost $blogPost, DeleteBlogPostAction $delete): RedirectResponse
    {
        $delete->execute($blogPost);

        Inertia::flash('toast', ['type' => 'success', 'message' => __(':name has been deleted.', ['name' => $blogPost->title])]);

        return back();
    }
}
