<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ReviewService $reviewService
    ) {}

    public function index(int $productId): JsonResponse
    {
        $reviews = $this->reviewService->getApprovedProductReviews($productId);

        return ReviewResource::collection($reviews)->response();
    }

    public function storeReviews(): JsonResponse
    {
        $data = $this->reviewService->getStoreServiceReviews();

        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id'     => ['nullable', 'exists:products,id'],
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'title'          => ['nullable', 'string', 'max:255'],
            'comment'        => ['required', 'string', 'min:10'],
            'reviewer_name'  => ['nullable', 'string', 'max:255'],
            'reviewer_email' => ['nullable', 'email', 'max:255'],
            'photos'         => ['nullable', 'array'],
            'photos.*'       => ['image', 'max:2048'],
        ]);

        $review = $this->reviewService->createReview($validated, $request->user('sanctum'));

        return response()->json([
            'message' => 'Thank you for your review! It has been posted.',
            'data'    => new ReviewResource($review->load('media')),
        ], 201);
    }

    public function vote(Request $request, int $id): JsonResponse
    {
        $voted = $this->reviewService->voteHelpful($id, $request->ip(), $request->user('sanctum'));

        if (! $voted) {
            return response()->json(['message' => 'You have already voted on this review.'], 422);
        }

        return response()->json(['message' => 'Thank you for your feedback!']);
    }
}
