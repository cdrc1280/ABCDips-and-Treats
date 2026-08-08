<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = BlogPost::with('author:id,name')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);

        return response()->json([
            'data' => $posts->items(),
            'meta' => [
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'current_page' => $posts->currentPage(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::with('author:id,name')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (!$post) {
            return response()->json(['message' => 'Blog post not found.'], 404);
        }

        return response()->json(['data' => $post]);
    }
}
