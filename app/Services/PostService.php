<?php

namespace App\Services;

use App\Events\PostCreated;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Exceptions\UnauthorizationException;
use App\Models\Post;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function getRestaurantPosts(int $restaurant_id)
    {
        // 投稿とユーザー情報の結合
        $posts = Post::selectRaw('posts.id as post_id, posts.title, posts.visited_at, posts.period_of_time, posts.points, posts.price_min, posts.price_max, posts.image_url1, posts.created_at, users.id as user_id, users.user_name, users.follower_num, users.post_num')
                        ->join('users', 'posts.user_id', '=', 'users.id')
                        ->where('posts.restaurant_id', $restaurant_id)
                        ->get();

        if ($posts->isEmpty()) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $response_data = [];
        foreach ($posts as $post) {
            $post_created_datetime = new Carbon($post->created_at);
            $post_created_date     = $post_created_datetime->format('Y-m-d');
            $response_data[] = [
                'user' => [
                    'id'           => $post->user_id,
                    'user_name'    => $post->user_name,
                    'follower_num' => $post->follower_num,
                    'post_num'     => $post->post_num
                ],
                'post' => [
                    'id'             => $post->post_id,
                    'title'          => $post->title,
                    'visited_at'     => $post->visited_at,
                    'period_of_time' => $post->period_of_time,
                    'points'         => $post->points,
                    'price_min'      => $post->price_min,
                    'price_max'      => $post->price_max,
                    'image_url'      => $post->image_url1,
                    'created_at'     => $post_created_date
                ]
            ];
        }

        return [
            'posts' => $response_data
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
            $images = [
                $post->image_url1,
                $post->image_url2,
                $post->image_url3,
            ];
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
                'images'          => $images,
                'created_at'      => $created_date
            ];
        }

        return $response_data;
    }

    public function getPost(int $post_id)
    {
        $post = Post::where('posts.id', $post_id)
                    ->join('users', 'posts.user_id', '=', 'users.id')
                    ->join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
                    ->first();

        if (!$post) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        $created_datetime = new Carbon($post->created_at);
        $created_date     = $created_datetime->format('Y-m-d');
        $images = [
            $post->image_url1,
            $post->image_url2,
            $post->image_url3,
        ];
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
            'images'          => $images,
            'created_at'      => $created_date
        ];

        return $response_data;
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

        $post = Post::create($insert_data);

        if ( !$post ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // 投稿作成時のイベント
        // 1. 店の平均点を再計算
        // 2. ユーザーの店の訪問数を再計算
        event(new PostCreated(Auth::user(), $restaurant_id, $post));

        return [
            'ok' => true
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
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // 変更前の点数
        $before_points = $post->points;

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
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // 点数に変更があった場合
        if ($before_points != $points) {

            $restaurant = Restaurant::where('id', $post->restaurant_id)->first();
            if (!$restaurant) {
                throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
            }

            // 変更点数の差分
            $diff_point = $points - $before_points;

            // 平均点数の再計算
            $restaurant->point_avg = round((($restaurant->point_avg * $restaurant->post_num) + $diff_point) / $restaurant->post_num, 1);

            // セーブ
            if (!$restaurant->save()) {
                throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
            }

        }

        return [
            'ok' => true
        ];
    }

    public function deletePost(Post $post)
    {
        // ユーザーチェック
        $check = Gate::inspect('delete', $post);
        if ( $check->denied() ) {
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // 削除する投稿の店ID、点数、投稿数取得
        $restaurant_id = $post->restaurant_id;
        $delete_point  = $post->points;


        if ( !$post->delete() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // 店の投稿数、平均点数を更新
        $restaurant = Restaurant::where('id', $restaurant_id)->first();
        if ( !$restaurant ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }
        $restaurant->point_avg = round((($restaurant->point_avg * $restaurant->post_num) - $delete_point) / ($restaurant->post_num - 1), 1);
        $restaurant->post_num--;
        if ( !$restaurant->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'ok' => true
        ];
    }


}
