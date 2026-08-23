<?php

namespace App\Policies\User;

use App\Models\User;

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
        return $authUser->hasRole(['admin', 'super_admin']) ||
            $authUser->id === $user->id;
    }

    public function deleteUser(User $authUser, User $user)
    {
        // no deleting owner
        if ($user->hasRole('super_admin')) {
            return false;
        }

        // no deleting self
        if ($authUser->id === $user->id) {
            return false;
        }

        return $authUser->hasRole(['admin', 'super_admin']);
    }
    public function updateRole(User $authUser, User $user)
    {
        // no updating owner
        if ($user->hasRole('super_admin')) {
            return false;
        }

        // no updating self
        if ($authUser->id === $user->id) {
            return false;
        }

        return $authUser->hasRole(['admin', 'super_admin']);
    }

    public function manage(User $authUser)
    {
        return $authUser->hasRole(['manager', 'super_admin', 'admin']);
    }
    public function get(User $authUser, User $user)
    {
        return $authUser->hasRole(['super_admin', 'admin'])
            || $authUser->id === $user->id;
    }
}
