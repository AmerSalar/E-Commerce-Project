<?php

namespace App\Http\Controllers\Auth;

use App\DTO\AuthData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\Auth\AuthenticatedResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

        $data = new AuthData(
            user: $user,
            token: $token,
            message: "Registered successfully.",
        );

        return new AuthenticatedResource($data);
    }
}
