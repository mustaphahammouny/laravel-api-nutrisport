<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\User;
use App\Notifications\OrderCreatedAdminNotification;
use App\Notifications\OrderCreatedCustomerNotification;

class SendOrderCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order->loadMissing(['site', 'customer', 'items']);

        $order->customer->notify(new OrderCreatedCustomerNotification($order));

        User::query()
            ->where('id', 1)
            ->orWhere('can_view_orders', true)
            ->get()
            ->each(fn(User $user) => $user->notify(new OrderCreatedAdminNotification($order)));
    }
}
