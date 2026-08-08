<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'category' => 'required|in:product_idea,service_feedback,feature_request,other',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        if (auth('sanctum')->check()) {
            $validated['user_id'] = auth('sanctum')->id();
        }

        $suggestion = Suggestion::create($validated);

        return response()->json([
            'message' => 'Your suggestion has been submitted successfully!',
            'data' => $suggestion
        ], 201);
    }
}
