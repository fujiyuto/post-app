<?php

namespace App\Services;

class PostService {

    public function getPosts()
    {

    }

    public function getUserPosts(int $user_id)
    {

    }

    public function getPost(int $post_id)
    {

    }

    public function createPost(
        int $user_id,
        int $restaurant_id,
        string $title,
        string $content,
        string $visited_at=null,
        int $period_of_time,
        float $points,
        int $price_min=null,
        int $price_max=null
    )
    {

    }

    public function updatePost(
        string $title,
        string $content,
        string $visited_at=null,
        int $period_of_time,
        float $points,
        int $price_min=null,
        int $price_max=null
    )
    {

    }

    public function deletePost(int $user_id, int $restaurant_id)
    {

    }


}
