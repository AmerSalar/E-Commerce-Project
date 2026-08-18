<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
                    Password::default()->letters()->numbers()->symbols()
                ]
            ]
        );

        Auth::user()->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response()->json(['message' => 'Password updated successfully.'], 200);
    }
}
