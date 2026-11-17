<?php

namespace App\Domains\Storefront\Controllers;

use App\Domains\Content\Models\ChatEscalation;
use App\Http\Controllers\Controller;
use App\Services\AiAdvisorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Customer chat widget. The thread belongs to the signed-in user, or to the guest's session —
 * never to an email typed into the browser. The browser only ever sends the customer's own
 * text; assistant and staff messages are written server-side.
 */
final class ChatController extends Controller
{
    private const HISTORY = 'chat.history';

    private const THREAD = 'chat.escalation_id';

    private const GUEST_LIMIT = 5;

    private const AUTO_ESCALATE_AFTER = 3;

    public function show(Request $request): JsonResponse
    {
        return response()->json($this->payload($request, $this->thread($request)));
    }

    public function send(Request $request, AiAdvisorService $ai): JsonResponse
    {
        $text = trim(strip_tags((string) $request->validate(['message' => ['required', 'string', 'min:2', 'max:1000']])['message']));
        $thread = $this->thread($request);

        // Escalated: the message goes straight to staff.
        if ($thread && $thread->status !== 'resolved') {
            $thread->update(['conversation' => [...($thread->conversation ?? []), ['role' => 'user', 'content' => $text]]]);

            return response()->json($this->payload($request, $thread));
        }

        $history = $request->session()->get(self::HISTORY, []);
        $asked = count(array_filter($history, fn ($m) => $m['role'] === 'user'));

        if (! $request->user() && $asked >= self::GUEST_LIMIT) {
            return response()->json(['message' => __('Sign in to keep chatting, or leave your details and our team will reply.')], 429);
        }

        $history[] = ['role' => 'user', 'content' => $text];
        $answer = $ai->ask($text, 'customer');
        $history[] = ['role' => 'assistant', 'content' => (string) ($answer['response'] ?? __('Sorry, I could not answer that right now.'))];
        $request->session()->put(self::HISTORY, array_slice($history, -40));

        if ($asked + 1 >= self::AUTO_ESCALATE_AFTER) {
            $thread = $this->escalateHistory($request, $request->user()?->name, $request->user()?->email);
        }

        return response()->json($this->payload($request, $thread));
    }

    public function escalate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'email' => [$request->user() ? 'nullable' : 'required', 'email', 'max:255'],
        ]);

        $thread = $this->escalateHistory($request, $validated['name'] ?? null, $validated['email'] ?? null);

        return response()->json($this->payload($request, $thread));
    }

    private function thread(Request $request): ?ChatEscalation
    {
        if ($user = $request->user()) {
            return ChatEscalation::query()->where('user_id', $user->id)->latest()->first();
        }

        $id = $request->session()->get(self::THREAD);

        return is_int($id) || is_string($id) ? ChatEscalation::query()->whereNull('user_id')->whereKey($id)->first() : null;
    }

    private function escalateHistory(Request $request, ?string $name, ?string $email): ChatEscalation
    {
        $history = $request->session()->pull(self::HISTORY, []);
        $notice = ['role' => 'assistant', 'content' => __('I’ve passed this conversation to our team — a person will reply here shortly.')];
        $messages = [...($history ?: [['role' => 'user', 'content' => __('Customer asked for human support.')]]), $notice];
        $user = $request->user();

        $thread = $this->thread($request);

        if ($thread) {
            $thread->update([
                'conversation' => [...($thread->conversation ?? []), ...$messages],
                'status' => 'open',
                'guest_name' => $thread->guest_name ?: ($name ?: $user?->name),
                'guest_email' => $thread->guest_email ?: ($email ?: $user?->email),
            ]);
        } else {
            $thread = ChatEscalation::create([
                'user_id' => $user?->id,
                'guest_name' => $name ?: $user?->name ?: 'Guest Customer',
                'guest_email' => $email ?: $user?->email,
                'conversation' => $messages,
                'status' => 'open',
            ]);
        }

        if (! $user) {
            $request->session()->put(self::THREAD, $thread->id);
        }

        return $thread;
    }

    /**
     * @return array{escalated: bool, status: ?string, messages: list<array{role: string, content: string}>, guest_limit_reached: bool}
     */
    private function payload(Request $request, ?ChatEscalation $thread): array
    {
        $escalated = $thread && $thread->status !== 'resolved';
        $history = $request->session()->get(self::HISTORY, []);
        $messages = $escalated ? ($thread->conversation ?? []) : $history;

        return [
            'escalated' => $escalated,
            'status' => $thread?->status,
            'messages' => array_values(array_map(fn ($m) => ['role' => (string) ($m['role'] ?? 'assistant'), 'content' => (string) ($m['content'] ?? '')], $messages)),
            'guest_limit_reached' => ! $request->user() && ! $escalated
                && count(array_filter($history, fn ($m) => $m['role'] === 'user')) >= self::GUEST_LIMIT,
        ];
    }
}
