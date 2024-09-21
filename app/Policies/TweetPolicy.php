<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tweet;

class TweetPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Tweet $tweet)
    {
        return $user->id === $tweet->user_id;
    }

    public function delete(User $user, Tweet $tweet)
    {
        return $user->id === $tweet->user_id;
    }
}
