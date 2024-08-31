<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Models\Post;
use Carbon\Carbon;

class PostService
{
    public function getPosts()
    {
        $posts = Post::join('users', 'posts.user_id', '=', 'users.id')
                    ->join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
                    ->orderByDesc('posts.id')
                    ->get();

        if ( $posts->isEmpty() ) {
            throw new DataNotFoundException('投稿一覧取得エラー');
        }

        $response_data = [];
        foreach ($posts as $post) {
            $created_datetime = new Carbon($post->created_at);
            $created_date     = $created_datetime->format('Y-m-d');
            $response_data[] = [
                'id'              => $post->id,
                'user_id'         => $post->user_id,
                'user_name'       => $post->user_name,
                'restaurant_id'   => $post->restaurant_id,
                'restaurant_name' => $post->restaurant_name,
                'title'           => $post->title,
                'content'         => $post->content,
                'visited_at'      => $post->visited_at,
                'period_of_time'  => $post->period_of_time,
                'points'          => $post->points,
                'price_min'       => $post->price_min,
                'price_max'       => $post->price_max,
                'created_at'      => $created_date
            ];
        }

        return [
            'data' => [
                'posts' => $response_data
            ]
        ];
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
        string $visited_at = null,
        int $period_of_time,
        float $points,
        int $price_min = null,
        int $price_max = null
    ) {

    }

    public function updatePost(
        string $title,
        string $content,
        string $visited_at = null,
        int $period_of_time,
        float $points,
        int $price_min = null,
        int $price_max = null
    ) {

    }

    public function deletePost(int $user_id, int $restaurant_id)
    {

    }


}
