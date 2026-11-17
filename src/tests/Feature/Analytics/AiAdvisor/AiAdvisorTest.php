<?php

namespace Tests\Feature\Analytics\AiAdvisor;

use App\Domains\Access\Enums\AccessPermission;
use App\Domains\Analytics\Enums\AiAdvisorPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AiAdvisorTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_the_permission(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView))->get('/admin/ai-advisor')->assertForbidden();
    }

    public function test_answers_with_the_configured_gemini_model_and_strips_markup(): void
    {
        config(['services.gemini.key' => 'test-key', 'services.gemini.model' => 'gemini-test']);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['text' => 'Reorder flour.']]]]]])]);
        $this->actingAs($this->userWith(AccessPermission::DashboardView, AiAdvisorPermission::AiAdvisorUse));

        $this->get('/admin/ai-advisor')->assertInertia(fn (Assert $page) => $page->component('ai-advisor/index')->where('configured', true));

        $this->from('/admin/ai-advisor')
            ->post('/admin/ai-advisor', ['prompt' => '<b>What</b> should I reorder?', 'category' => 'inventory'])
            ->assertRedirect('/admin/ai-advisor')
            ->assertInertiaFlash('advisor.answer', 'Reorder flour.')
            ->assertInertiaFlash('advisor.question', 'What should I reorder?');

        Http::assertSent(fn ($request) => str_contains($request->url(), 'models/gemini-test:generateContent'));
    }

    public function test_validates_the_question(): void
    {
        $this->actingAs($this->userWith(AccessPermission::DashboardView, AiAdvisorPermission::AiAdvisorUse));
        $this->post('/admin/ai-advisor', ['prompt' => '<i></i>'])->assertSessionHasErrors('prompt');
    }
}
