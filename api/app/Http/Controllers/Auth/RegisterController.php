<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    public function __invoke(RegisterUserRequest $request)
    {
        $request->validated();

        $attributes = $request->only(
            ['name', 'email', 'password']
        );

        $user = User::create($attributes);

        $token = Auth::login($user);

        return response()->json([
            'message' => 'Registered successfully.',
            'token' => $token,
            'user' => $user
        ]);
    }
}
