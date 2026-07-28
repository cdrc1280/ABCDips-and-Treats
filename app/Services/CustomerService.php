<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerService
{
    public function updateProfile(User $user, array $data): User
    {
        $user->update([
            'name'    => $data['name'] ?? $user->name,
            'phone'   => $data['phone'] ?? $user->phone,
            'address' => $data['address'] ?? $user->address,
        ]);

        return $user->fresh();
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided current password does not match our records.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function updateAvatar(User $user, UploadedFile $file): User
    {
        $user->addMedia($file)->toMediaCollection('avatar');
        return $user->fresh();
    }
}
