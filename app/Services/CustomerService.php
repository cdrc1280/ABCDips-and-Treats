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
            'name'           => $data['name'] ?? $user->name,
            'phone'          => $data['phone'] ?? $user->phone,
            'address'        => $data['address'] ?? $user->address,
            'city'           => $data['city'] ?? $user->city,
            'region'         => $data['region'] ?? $user->region,
            'province'       => $data['province'] ?? $user->province,
            'barangay'       => $data['barangay'] ?? $user->barangay,
            'street_address' => $data['street_address'] ?? $user->street_address,
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
