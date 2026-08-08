<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatEscalation;
use Illuminate\Http\Request;

class ChatEscalationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'nullable|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'conversation' => 'required|array',
            'conversation.*.role' => 'required|in:user,assistant,admin',
            'conversation.*.content' => 'required|string',
        ]);

        $userId = auth('sanctum')->check() ? auth('sanctum')->id() : null;
        $guestEmail = $validated['guest_email'] ?? null;
        $guestName = $validated['guest_name'] ?? null;

        // 1. Consolidate to ONE row per client: search for existing escalation record
        $escalation = null;

        if ($userId) {
            $escalation = ChatEscalation::where('user_id', $userId)->latest()->first();
        } elseif (!empty($guestEmail)) {
            $escalation = ChatEscalation::where('guest_email', $guestEmail)->latest()->first();
        }

        if ($escalation) {
            // Append / merge new conversation messages into single record
            $existingConv = $escalation->conversation ?? [];
            $newConv = $validated['conversation'];

            $merged = $existingConv;
            foreach ($newConv as $item) {
                $exists = false;
                foreach ($existingConv as $e) {
                    if (($e['role'] ?? '') === ($item['role'] ?? '') && ($e['content'] ?? '') === ($item['content'] ?? '')) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $merged[] = $item;
                }
            }

            $escalation->update([
                'guest_name' => $guestName ?: $escalation->guest_name,
                'guest_email' => $guestEmail ?: $escalation->guest_email,
                'user_id' => $userId ?: $escalation->user_id,
                'conversation' => array_values($merged),
                'status' => $escalation->status === 'resolved' ? 'open' : $escalation->status,
            ]);
        } else {
            // Create new single row for client
            $escalation = ChatEscalation::create([
                'user_id' => $userId,
                'guest_name' => $guestName ?: 'Guest Customer',
                'guest_email' => $guestEmail,
                'conversation' => $validated['conversation'],
                'status' => 'open',
            ]);
        }

        return response()->json([
            'message' => 'Your message has been sent to our team! We will get back to you shortly.',
            'data' => $escalation
        ], 200);
    }

    public function fetchClientConversation(Request $request)
    {
        $userId = auth('sanctum')->check() ? auth('sanctum')->id() : null;
        $guestEmail = $request->query('guest_email');

        $escalation = null;
        if ($userId) {
            $escalation = ChatEscalation::where('user_id', $userId)->latest()->first();
        } elseif (!empty($guestEmail)) {
            $escalation = ChatEscalation::where('guest_email', $guestEmail)->latest()->first();
        }

        if (!$escalation) {
            return response()->json([
                'id' => null,
                'status' => null,
                'conversation' => [],
            ]);
        }

        return response()->json([
            'id' => $escalation->id,
            'status' => $escalation->status,
            'conversation' => $escalation->conversation ?? [],
            'updated_at' => $escalation->updated_at?->toISOString(),
        ]);
    }
}
