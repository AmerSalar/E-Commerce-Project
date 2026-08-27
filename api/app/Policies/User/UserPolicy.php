<?php

namespace App\Policies\User;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function updateName(User $authUser, User $user)
    {
        // no updating name for owner, only he can do it for himself

        if ($user->hasRole('super_admin')) {

            return $authUser->id === $user->id
                ? Response::allow()
                // if it was admin, deny here before moving to next check
                : Response::deny("You do not have permission to update name of this user!");
        }

        return $authUser->hasRole(['admin', 'super_admin']) ||
            $authUser->id === $user->id
            ? Response::allow()
            : Response::deny("You do not have permission to update name of this user!");
    }

    public function deleteUser(User $authUser, User $user)
    {
        // no deleting owner
        if ($user->hasRole('super_admin')) {
            return Response::deny("You do not have permission to delete this user!");
        }

        // no deleting self
        if ($authUser->id === $user->id) {
            return Response::deny("You do not have permission to delete your account!");
        }

        return $authUser->hasRole(['admin', 'super_admin'])
            ? Response::allow()
            : Response::deny("You do not have permission to delete this user!");
    }
    public function updateRole(User $authUser, User $user)
    {
        // no updating owner
        if ($user->hasRole('super_admin')) {
            return Response::deny("You do not have permission to update this user!");
        }

        // no updating self
        if ($authUser->id === $user->id) {
            return Response::deny("You do not have permission to update your role!");
        }

        return $authUser->hasRole(['admin', 'super_admin'])
            ? Response::allow()
            : Response::deny("You do not have permission to update this user!");
    }

    public function manage(User $authUser)
    {
        return $authUser->hasRole(['manager', 'super_admin', 'admin'])
            ? Response::allow()
            : Response::deny("You do not have permission!");
    }
    public function get(User $authUser, User $user)
    {
        return ($authUser->hasRole(['manager', 'super_admin', 'admin'])
            || $authUser->id === $user->id)
            ? Response::allow()
            : Response::deny("You do not have permission to access this user!");
    }
}
