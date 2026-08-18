<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\AuthenticatedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{

    public function __invoke(Request $request)
    {
        $request->validate(
            [
                'old_password' => ['required', 'current_password'],
                'password' => [
                    'required',
                    'confirmed',
                    'different:old_password',
                    Password::default()->letters()->numbers()->symbols()
                ]
            ]
        );

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        // true to force blacklisting forever
        Auth::invalidate(true);

        // generate new token
        $token = Auth::login($user);

        return new AuthenticatedResource(
            (object)[
                'message' => "Password updated successfully.",
                'token' => $token,
                'user' => $user
            ]
        );
    }
}
