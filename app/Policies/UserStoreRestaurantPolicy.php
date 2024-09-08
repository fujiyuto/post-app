<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserStoreRestaurant;

class UserStoreRestaurantPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function delete(User $user, UserStoreRestaurant $user_store_restaurant)
    {
        return $user->id === $user_store_restaurant->user_id;
    }
}
