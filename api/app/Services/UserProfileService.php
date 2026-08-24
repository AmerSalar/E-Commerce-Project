<?php

namespace App\Services;

use App\DTO\AuthData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserProfileService
{
    /**
     * Update name of user
     */
    public function updateProfile(User $user, array $validatedData): User
    {
        $user->update($validatedData);
        return $user->load(['roles', 'addresses']);
    }

    /**
     * change password of user
     */
    public function changePassword(User $user, string $validatedNewPassword): AuthData
    {
        return DB::transaction(function () use ($user, $validatedNewPassword) {
            $user->update([
                'password' => Hash::make($validatedNewPassword)
            ]);

            Auth::invalidate(true);

            $token = Auth::login($user);

            return new AuthData(
                message: "Password changed successfully.",
                user: $user,
                token: $token,
            );
        });
    }
}
