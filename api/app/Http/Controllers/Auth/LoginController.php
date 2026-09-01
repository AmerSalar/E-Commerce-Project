<?php

namespace App\Http\Controllers\Auth;

use App\DTO\AuthData;
use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Resources\Auth\AuthenticatedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * login
     */
    public function __invoke(LoginUserRequest $request)
    {
        $request->validated();

        $token = Auth::attempt($request->only(['email', 'password']));

        if (!$token) {
            return response()->json(
                ['message' => 'The provided credentials don\'t match our records!'],
                401
            );
        }

        $cookie = HelperFunctions::makeCookie(
            "token",
            $token,
            60 * 24
        );

        $data = new AuthData(
            user: Auth::user(),
            message: "logged-in successfully.",
        );

        return response()->json(
            new AuthenticatedResource($data),
            200
        )->withCookie($cookie);
    }
}
