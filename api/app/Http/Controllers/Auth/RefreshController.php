<?php

namespace App\Http\Controllers\Auth;

use App\DTO\AuthData;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\AuthenticatedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class RefreshController extends Controller
{
    /**
     * Refresh JWT token
     */
    public function __invoke(Request $request)
    {
        $request->validate(['refresh_token' => ['required', 'string']]);

        try {
            $token = Auth::setToken($request->input('refresh_token'))->refresh();
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token may be expired or invalid.'
            ], 401);
        }


        return response()->json([
            'message' => 'refreshed successfully.',
            'token' => $token
        ], 200);
    }
}
