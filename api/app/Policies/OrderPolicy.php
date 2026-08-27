<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function getOne(User $authUser, Order $order)
    {
        return ($authUser->hasRole(['admin', 'super_admin'])
            || $authUser->id === $order->user_id)
            ? Response::allow()
            : Response::deny("You do not have permission to access this order!");
    }
    public function deliver(User $authUser)
    {
        return $authUser->hasRole(['driver', 'admin', 'super_admin'])
            ? Response::allow()
            : Response::deny("You do not have permission to deliver this order!");
    }
}
