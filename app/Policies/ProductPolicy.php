<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    public function before(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $actor): bool
    {
        return $actor->can_create_products;
    }
}
