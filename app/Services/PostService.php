<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Exceptions\UnauthorizationException;
use App\Models\Post;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PostService
{
    public function getPosts()
    {

        $posts = Post::selectRaw('posts.id as post_id, user_id, restaurant_id, title, content, visited_at, period_of_time, points, posts.price_min, posts.price_max, image_url1, image_url2, image_url3, posts.created_at, users.user_name, restaurants.restaurant_name')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
        ->orderByDesc('posts.created_at')
        ->get();

        if ( $posts->isEmpty() ) {
            throw new DataNotFoundException('投稿一覧取得エラー');
        }

        $response_data = [];
        foreach ($posts as $post) {
            $post_created_datetime = new Carbon($post->created_at);
            $post_created_date     = $post_created_datetime->format('Y-m-d');
            $response_data[] = [
                'user' => [
                    'id'   => $post->user_id,
                    'name' => $post->user_name
                ],
                'restaurant' => [
                    'id'   => $post->restaurant_id,
                    'name' => $post->restaurant_name
                ],
                'post' => [
                    'id'             => $post->post_id,
                    'title'          => $post->title,
                    'content'        => $post->content,
                    'visited_at'     => $post->visited_at,
                    'period_of_time' => $post->period_of_time,
                    'points'         => $post->points,
                    'price_min'      => $post->price_min,
                    'price_max'      => $post->price_max,
                    'image_url1'     => $post->image_url1,
                    'image_url2'     => $post->image_url2,
                    'image_url3'     => $post->image_url3,
                    'created_at'     => $post_created_date
                ]
            ];
        }

        return [
            'data' => [
                'posts' => $response_data
            ]
        ];
    }

    public function getUserPosts(int $user_id, string $user_name)
    {
        $posts = Post::where('posts.user_id', $user_id)
                    ->join('users', 'posts.user_id', '=', 'users.id')
                    ->join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
                    ->orderByDesc('posts.id')
                    ->get();

        $response_data = [];
        $response_data['user'] = [
            'id'        => $user_id,
            'user_name' => $user_name
        ];
        $response_data['posts'] = [];
        foreach ($posts as $post) {
            $created_datetime = new Carbon($post->created_at);
            $created_date     = $created_datetime->format('Y-m-d');
            $response_data['posts'][]  = [
                'id'              => $post->id,
                'restaurant_id'   => $post->restaurant_id,
                'restaurant_name' => $post->restaurant_name,
                'title'           => $post->title,
                'content'         => $post->content,
                'visited_at'      => $post->visited_at,
                'period_of_time'  => $post->period_of_time,
                'points'          => $post->points,
                'price_min'       => $post->price_min,
                'price_max'       => $post->price_max,
                'image_url1'      => $post->image_url1,
                'image_url2'      => $post->image_url2,
                'image_url3'      => $post->image_url3,
                'created_at'      => $created_date
            ];
        }

        return [
            'data' => $response_data
        ];
    }

    public function getPost(int $post_id)
    {
        $post = Post::where('posts.id', $post_id)
                    ->join('users', 'posts.user_id', '=', 'users.id')
                    ->join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
                    ->first();

        if ( !$post ) {
            throw new DataNotFoundException('投稿取得エラー');
        }

        $created_datetime = new Carbon($post->created_at);
        $created_date     = $created_datetime->format('Y-m-d');
        $response_data = [
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
            'image_url1'      => $post->image_url1,
            'image_url2'      => $post->image_url2,
            'image_url3'      => $post->image_url3,
            'created_at'      => $created_date
        ];

        return [
            'data' => [
                'post' => $response_data
            ]
        ];
    }

    public function createPost(
        int    $user_id,
        int    $restaurant_id,
        string $title,
        string $content,
        string $visited_at = null,
        int    $period_of_time,
        float  $points,
        int    $price_min = null,
        int    $price_max = null,
        string $image_url1 = null,
        string $image_url2 = null,
        string $image_url3 = null,
    ) {
        $insert_data = [
            'user_id'        => $user_id,
            'restaurant_id'  => $restaurant_id,
            'title'          => $title,
            'content'        => $content,
            'visited_at'     => $visited_at,
            'period_of_time' => $period_of_time,
            'points'         => $points,
            'price_min'      => $price_min,
            'price_max'      => $price_max,
            'image_url1'     => $image_url1,
            'image_url2'     => $image_url2,
            'image_url3'     => $image_url3
        ];

        if ( !Post::create($insert_data) ) {
            throw new DataOperationException('投稿作成エラー');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function updatePost(
        Post   $post,
        string $title,
        string $content,
        string $visited_at = null,
        int    $period_of_time,
        float  $points,
        int    $price_min = null,
        int    $price_max = null,
        string $image_url1 = null,
        string $image_url2 = null,
        string $image_url3 = null,
    ) {
        // ユーザーチェック
        $check = Gate::inspect('update', $post);
        if ( $check->denied() ) {
            throw new UnauthorizationException('不正な投稿編集');
        }

        $post->title          = $title;
        $post->content        = $content;
        $post->visited_at     = $visited_at;
        $post->period_of_time = $period_of_time;
        $post->points         = $points;
        $post->price_min      = $price_min;
        $post->price_max      = $price_max;
        $post->image_url1     = $image_url1;
        $post->image_url2     = $image_url2;
        $post->image_url3     = $image_url3;

        if ( !$post->save() ) {
            throw new DataOperationException('投稿更新エラー');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deletePost(Post $post)
    {
        // ユーザーチェック
        $check = Gate::inspect('delete', $post);
        if ( $check->denied() ) {
            throw new UnauthorizationException('不正な投稿削除');
        }

        if ( !$post->delete() ) {
            throw new DataOperationException('投稿削除エラー');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }


}
