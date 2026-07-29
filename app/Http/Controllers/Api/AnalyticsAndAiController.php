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
        $prompt = $request->input('prompt') ?: $request->input('query') ?: $request->input('message');

        if (! $prompt || strlen(trim($prompt)) < 2) {
            return response()->json(['message' => 'Please enter a valid question or message.'], 422);
        }

        $category = $request->input('category', 'general');
        $result   = $this->aiAdvisorService->ask(trim($prompt), $category);

        return response()->json([
            'data'    => $result,
            'message' => $result['response'] ?? '',
        ]);
    }
}
