<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Order;

class OrderPolicy
{
    public function view(Customer $customer, Order $order): bool
    {
        return $order->customer->id === $customer->id;
    }
}
