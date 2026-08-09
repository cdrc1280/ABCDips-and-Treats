<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomOrderApiResource;
use App\Models\CustomOrder;
use App\Services\CustomOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomOrderController extends Controller
{
    public function __construct(
        private readonly CustomOrderService $customOrderService
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'servings_count' => ['required', 'integer', 'min:1'],
            'tiers_count' => ['required', 'integer', 'min:1', 'max:10'],
            'flavor_preference' => ['nullable', 'string', 'max:255'],
            'frosting_type' => ['nullable', 'string', 'max:255'],
            'theme_description' => ['required', 'string', 'min:5'],
            'budget_range_min' => ['nullable', 'numeric', 'min:0'],
            'budget_range_max' => ['nullable', 'numeric', 'min:0'],
            'reference_photos' => ['nullable', 'array'],
            'reference_photos.*' => ['file', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);

        $customOrder = $this->customOrderService->createInquiry($validated, $request->user('sanctum'));

        return response()->json([
            'message' => 'Your custom bakery inquiry has been submitted! Our head pastry chef will contact you within 24 hours with a quote.',
            'data' => new CustomOrderApiResource($customOrder->load('media')),
        ], 201);
    }

    public function myCustomOrders(Request $request): JsonResponse
    {
        $orders = CustomOrder::where('user_id', $request->user()->id)
            ->with(['media'])
            ->latest()
            ->get();

        return response()->json([
            'data' => CustomOrderApiResource::collection($orders),
        ]);
    }
}
