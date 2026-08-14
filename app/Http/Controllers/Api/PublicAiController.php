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
        $request->validate([
            'prompt' => ['nullable', 'string', 'max:2000'],
            'query'  => ['nullable', 'string', 'max:2000'],
        ]);

        $prompt = $request->input('prompt') ?? $request->input('query');

        if (! $prompt || ! is_string($prompt) || strlen(trim($prompt)) < 2) {
            return response()->json([
                'message' => 'The prompt field is required.',
                'errors' => ['prompt' => ['The prompt or query field must be at least 2 characters.']],
            ], 422);
        }

        // Strip HTML/script tags to prevent prompt injection via markup
        $prompt = strip_tags(trim($prompt));

        $response = $this->aiAdvisorService->ask($prompt, 'customer');

        return response()->json($response);
    }
}
