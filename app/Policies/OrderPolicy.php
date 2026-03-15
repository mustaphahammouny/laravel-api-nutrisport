<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can_view_orders;
    }

    public function view(User|Customer $actor, Order $order): bool
    {
        return match (true) {
            $actor instanceof User => $actor->can_view_orders,
            $actor instanceof Customer => $order->customer_id === $actor->id,
            default => false,
        };
    }
}
