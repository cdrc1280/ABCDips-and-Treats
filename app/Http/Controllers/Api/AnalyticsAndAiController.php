<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAdvisorService;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsAndAiController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
        private readonly AiAdvisorService $aiAdvisorService
    ) {
    }

    public function analytics(Request $request): JsonResponse
    {
        return response()->json([
            'executive_summary' => $this->analyticsService->getExecutiveSummary(),
            'revenue_chart' => $this->analyticsService->getMonthlyRevenueChartData(),
            'top_products' => $this->analyticsService->getTopSellingProducts(),
        ]);
    }

    public function aiQuery(Request $request): JsonResponse
    {
        $prompt = $request->input('prompt') ?: $request->input('query') ?: $request->input('message');

        if (!$prompt || strlen(trim($prompt)) < 2) {
            return response()->json(['message' => 'Please enter a valid question or message.'], 422);
        }

        $category = $request->input('category', 'general');
        // Defense-in-depth: ensure the requester is an admin
        $user = $request->user();
        if (!$user || !$user->hasAnyRole(['super_admin', 'admin'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Audit log the query (limit stored prompt length)
        try {
            Log::info('AI query requested', [
                'user_id' => $user->id,
                'role' => $user->getRoleNames()->implode(','),
                'prompt' => is_string($prompt) ? (mb_strimwidth(trim($prompt), 0, 500, '...')) : null,
            ]);
        } catch (\Throwable $e) {
            // Logging failures should not block the request
        }

        $result = $this->aiAdvisorService->ask(trim($prompt), $category);

        return response()->json([
            'data' => $result,
            'message' => $result['response'] ?? '',
        ]);
    }
}
