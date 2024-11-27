<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use App\Models\Restaurant;
use Carbon\Carbon;

class LikeService {

    public function getLikePosts(User $user)
    {
        $likes_post_id_list = Like::where('user_id', $user->id)->pluck('post_id');

        $posts = Post::selectRaw('posts.id as post_id, user_id, restaurant_id, title, content, visited_at, period_of_time, points, posts.price_min, posts.price_max, image_url1, image_url2, image_url3, posts.created_at, users.user_name, restaurants.restaurant_name')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
        ->whereIn('posts.id', $likes_post_id_list)
        ->orderByDesc('posts.created_at')
        ->get();

        $response_data = [];
        foreach ($posts as $post) {
            $post_create_datetime = new Carbon($post->created_at);
            $post_create_date     = $post_create_datetime->format('Y-m-d');

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
                    'created_at'     => $post_create_date
                ]
            ];
        }

        return [
            'data' => [
                'posts' => $response_data
            ]
        ];
    }

    public function getLikeUsers(Post $post)
    {
        $like_users = User::selectRaw('user_name, gender, user_id')
                            ->join('likes', 'likes.user_id', '=', 'users.id')
                            ->where('post_id', $post->id)
                            ->orderByDesc('likes.created_at')
                            ->get();

        $response_data = [];
        foreach ($like_users as $user) {
            $response_data[] = [
                "id"     => $user->user_id,
                "name"   => $user->user_name,
                "gender" => $user->gender,
            ];
        }

        return [
            'data' => [
                'users' => $response_data
            ]
        ];
    }

    public function createLike(int $user_id, int $post_id)
    {
        $insert_data = [
            'user_id' => $user_id,
            'post_id' => $post_id
        ];

        if ( !Like::create($insert_data) ) {
            throw new DataOperationException('いいね作成失敗');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deleteLike(int $user_id, int $post_id)
    {
        $delete_like = Like::where([
                            'user_id' => $user_id,
                            'post_id' => $post_id
                        ])
                        ->first();
        if ( !$delete_like ) {
            throw new DataNotFoundException('削除するいいねがありません');
        }

        if ( !$delete_like->delete() ) {
            throw new DataOperationException('いいね削除に失敗');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }
}
