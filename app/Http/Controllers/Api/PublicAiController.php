<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAdvisorService;
use Illuminate\Http\Request;

class PublicAiController extends Controller
{
    protected AiAdvisorService $aiAdvisorService;

    public function __construct(AiAdvisorService $aiAdvisorService)
    {
        $this->aiAdvisorService = $aiAdvisorService;
    }

    public function query(Request $request)
    {
        $prompt = $request->input('prompt') ?? $request->input('query');

        if (! $prompt || ! is_string($prompt) || strlen(trim($prompt)) < 2) {
            return response()->json([
                'message' => 'The prompt field is required.',
                'errors' => ['prompt' => ['The prompt or query field must be at least 2 characters.']],
            ], 422);
        }

        $response = $this->aiAdvisorService->ask(trim($prompt), 'customer');

        return response()->json($response);
    }
}
