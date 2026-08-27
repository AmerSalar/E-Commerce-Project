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
    /**
     * get all users
     */
    public function index(Request $request)
    {
        Gate::authorize('manage');
        $perPage = max(1, min((int) $request->query('perPage', 10), 50));

        $users = User::query()->withRelations($request->query("include", 'roles'))
            ->paginate($perPage);

        return new UserCollection($users);
    }

    /**
     * get one user
     */
    public function show(User $user)
    {
        Gate::authorize('get', $user);
        $user->loadRelations(['roles', 'addresses']);
        return new UserResource($user);
    }

    /**
     * update name of user
     */
    public function update(UpdateNameRequest $request, User $user)
    {
        Gate::authorize('updateName', $user);

        $validated = $request->validated();
        $user->update($validated);

        return response()->json([
            'message' => 'user updated successfully.',
            'user' => new UserResource($user)
        ], 200);
    }

    public function assign(User $user, int $role_id)
    {
        Gate::authorize('updateRole', $user);

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
            'user' => new UserResource($user->loadRelations('roles'))
        ], 200);
    }
    /**
     * revoke a role from user
     */
    public function revoke(User $user, int $role_id)
    {
        Gate::authorize('updateRole', $user);

        $detached = $user->roles()->detach($role_id);

        if ($detached === 0) {
            return response()->json([
                'message' => 'User role not found!',
            ], 404);
        }

        return response()->json([
            'message' => 'user role revoked successfully.',
            'user' => new UserResource($user->loadRelations('roles'))
        ], 200);
    }

    /**
     * destroy a user
     */
    public function destroy(User $user)
    {
        Gate::authorize('deleteUser', $user);

        $user->delete();

        return response()->json([
            'message' => 'user deleted successfully.'
        ], 200);
    }
}
