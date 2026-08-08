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
            'conversation.*.role' => 'required|in:user,assistant',
            'conversation.*.content' => 'required|string',
        ]);

        if (auth('sanctum')->check()) {
            $validated['user_id'] = auth('sanctum')->id();
        }

        $escalation = ChatEscalation::create($validated);

        return response()->json([
            'message' => 'Your message has been sent to our team! We will get back to you shortly.',
            'data' => $escalation
        ], 201);
    }
}
