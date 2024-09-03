<?php

namespace App\Policies;

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

    public function update(User $auth_user, User $user)
    {
        return $auth_user->id === $user->id;
    }

    public function delete(User $auth_user, User $user)
    {
        return $auth_user->id === $user->id;
    }
}
