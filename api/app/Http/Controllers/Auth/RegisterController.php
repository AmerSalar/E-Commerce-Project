<?php

namespace App\Http\Controllers\Auth;

use App\DTO\AuthData;
use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\Auth\AuthenticatedResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function __invoke(RegisterUserRequest $request)
    {
        $request->validated();

        $attributes = $request->only(
            ['name', 'email', 'password']
        );

        $user = DB::transaction(function () use ($attributes) {
            $user = User::create($attributes);
            $user->cart()->create();

            return $user;
        });
        $token = Auth::login($user);

        $cookie = HelperFunctions::makeCookie(
            'token',
            $token,
            24 * 60
        );

        $data = new AuthData(
            user: $user,
            message: "Registered successfully.",
        );

        return response()->json(
            new AuthenticatedResource($data)
            ,
            201
        )->withCookie($cookie);
    }
}
