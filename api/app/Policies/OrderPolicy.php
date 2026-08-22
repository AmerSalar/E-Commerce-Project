<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function cancel(User $authUser, Order $order)
    {
        return $authUser->hasRole(['admin', 'super_admin'])
            || $authUser->id === $order->user_id;
    }
    public function deliver(User $authUser)
    {
        return $authUser->hasRole(['driver', 'admin', 'super_admin']);
    }
}
