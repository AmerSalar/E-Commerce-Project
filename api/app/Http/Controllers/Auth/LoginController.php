<?php

namespace App\Http\Controllers\Auth;

use App\DTO\AuthData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Resources\Auth\AuthenticatedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
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

        $data = new AuthData(
            user: Auth::user(),
            token: $token,
            message: "logged-in successfully.",
        );

        return new AuthenticatedResource($data);
    }
}
