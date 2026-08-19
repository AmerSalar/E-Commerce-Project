<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $users = User::with(['addresses', 'roles'])->paginate($perPage);

        return new UserCollection($users);
    }

    public static function notFound()
    {
        return response()->json([
            'message' => 'User not found!'
        ], 404);
    }
}
