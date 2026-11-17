<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Review\Models\Review;
use App\Domains\Storefront\Data\ReviewSubmissionData;
use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function store(Request $request, ReviewSubmissionData $data): RedirectResponse
    {
        $this->reviews->createReview([
            'product_id' => $data->productId,
            'rating' => $data->rating,
            'title' => $data->title,
            'comment' => $data->comment,
            'is_anonymous' => $data->isAnonymous,
            'reviewer_name' => $data->reviewerName,
            'reviewer_email' => $data->reviewerEmail,
            'photos' => $data->photos,
        ], $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Thanks for your review! It will appear once our team has checked it.')]);

        return back();
    }

    public function helpful(Request $request, Review $review): RedirectResponse
    {
        $voted = $this->reviews->voteHelpful($review->id, (string) $request->ip(), $request->user());

        Inertia::flash('toast', $voted
            ? ['type' => 'success', 'message' => __('Thanks for the feedback!')]
            : ['type' => 'info', 'message' => __('You already marked this review.')]);

        return back();
    }
}
