<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'    => ['sometimes', 'string', 'max:255'],
            'phone'   => ['sometimes', 'nullable', 'string', 'max:20'],
            'address' => ['sometimes', 'nullable', 'string'],
        ]);

        $user = $this->customerService->updateProfile($request->user(), $validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data'    => new UserResource($user),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?~`]).{8,}$/'
            ],
        ], [
            'new_password.regex' => 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&* etc.).',
            'new_password.min'   => 'Password must be at least 8 characters long.',
        ]);

        $this->customerService->changePassword($request->user(), $request->current_password, $request->new_password);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $user = $this->customerService->updateAvatar($request->user(), $request->file('avatar'));

        return response()->json([
            'message' => 'Avatar updated successfully.',
            'data'    => new UserResource($user),
        ]);
    }
}
