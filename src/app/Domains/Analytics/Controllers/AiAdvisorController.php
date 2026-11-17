<?php

namespace App\Domains\Analytics\Controllers;

use App\Domains\Analytics\Data\AiQuestionData;
use App\Domains\Analytics\Enums\AiAdvisorPermission;
use App\Http\Controllers\Controller;
use App\Services\AiAdvisorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class AiAdvisorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:'.AiAdvisorPermission::AiAdvisorUse->value)];
    }

    public function index(): Response
    {
        return Inertia::render('ai-advisor/index', [
            'configured' => (bool) (config('services.gemini.key') || config('services.openai.key')),
        ]);
    }

    /** The answer comes back as flash data; the conversation lives in the page. */
    public function ask(AiQuestionData $data, AiAdvisorService $advisor): RedirectResponse
    {
        $result = $advisor->ask($data->prompt, $data->category);

        Inertia::flash('advisor', [
            'id' => (string) str()->uuid(),
            'question' => $data->prompt,
            'answer' => (string) ($result['response'] ?? ''),
            'source' => (string) ($result['source'] ?? ''),
        ]);

        return back();
    }
}
