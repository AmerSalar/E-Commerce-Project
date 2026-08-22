<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateNameRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        $perPage = $request->query('perPage', 10);
        $include = $request->query('include');
        if ($include === "addresses") {
            $users = User::with(['roles', 'addresses'])->paginate($perPage);
        } else {
            $users = User::with('roles')->paginate($perPage);
        }
        return new UserCollection($users);
    }

    public function getOne(User $user)
    {
        $user->load(['roles', 'addresses']);
        return new UserResource($user);
    }

    public function updateName(UpdateNameRequest $request, User $user)
    {
        if (Gate::denies('updateName', $user)) {
            return response()->json([
                'message' => 'You are not allowed to change name of this user!'
            ], 403);
        }

        $validated = $request->validated();
        $user->update($validated);

        return response()->json([
            'message' => 'user updated successfully.',
            'user' => new UserResource($user)
        ], 200);
    }
    public function assignRole(User $user, int $role_id)
    {
        if (Gate::denies('updateRole', $user)) {
            return response()->json([
                'message' => 'You cannot assign roles to this user!'
            ], 403);
        };

        $role = Role::where('id', $role_id)->first();
        if (!$role) {
            return response()->json([
                'message' => 'This role does not exist!'
            ], 404);
        }

        if ($role->name === "super_admin") {
            return response()->json([
                'message' => 'You cannot assign this role!'
            ], 403);
        }

        // if user already has role, just pass an OK status
        $user->roles()->syncWithoutDetaching($role_id);

        return response()->json([
            'message' => 'user role assigned successfully.',
            'user' => new UserResource($user->load('roles'))
        ], 200);
    }
    public function revokeRole(User $user, int $role_id)
    {
        if (Gate::denies('updateRole', $user)) {
            return response()->json([
                'message' => 'You cannot revoke roles of this user!'
            ], 403);
        };

        $detached = $user->roles()->detach($role_id);

        if ($detached === 0) {
            return response()->json([
                'message' => 'User role not found!',
            ], 404);
        }

        return response()->json([
            'message' => 'user role revoked successfully.',
            'user' => new UserResource($user->load('roles'))
        ], 200);
    }

    public function destroy(User $user)
    {
        if (Gate::denies('deleteUser', $user)) {
            return response()->json([
                'message' => 'You cannot delete this user account!'
            ], 403);
        };

        $user->delete();

        return response()->json([
            'message' => 'user deleted successfully.'
        ], 200);
    }

    public static function notFound()
    {
        return response()->json([
            'message' => 'User not found!'
        ], 404);
    }
}
