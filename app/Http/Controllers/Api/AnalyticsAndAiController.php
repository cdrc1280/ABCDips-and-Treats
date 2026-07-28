<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAdvisorService;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsAndAiController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
        private readonly AiAdvisorService $aiAdvisorService
    ) {}

    public function analytics(Request $request): JsonResponse
    {
        return response()->json([
            'executive_summary' => $this->analyticsService->getExecutiveSummary(),
            'revenue_chart'     => $this->analyticsService->getMonthlyRevenueChartData(),
            'top_products'      => $this->analyticsService->getTopSellingProducts(),
        ]);
    }

    public function aiQuery(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt'   => ['required', 'string', 'min:3', 'max:500'],
            'category' => ['nullable', 'string'],
        ]);

        $result = $this->aiAdvisorService->ask($validated['prompt'], $validated['category'] ?? 'general');

        return response()->json([
            'data' => $result,
        ]);
    }
}
