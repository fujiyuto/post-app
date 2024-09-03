<?php

namespace App\Services;

use App\Models\User;
use App\Models\Follow;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\DataOperationException;
use App\Exceptions\UnauthorizationException;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class UserService
{
    public function getUser(User $user)
    {
        $response_data = [];

        $response_data['user'] = [
            'user_name'         => $user->user_name,
            'email'             => $user->email,
            'is_email_verified' => !is_null($user->email_verified_at),
            'gender'            => $user->gender,
            'user_type'         => $user->user_type
        ];

        // フォロワー数
        $follower_count = Follow::where('follower_id', $user->id)->count();
        // フォロー数
        $follow_count   = Follow::where('follow_id', $user->id)->count();
        $response_data['follow'] = [
            'follower_count' => $follower_count,
            'follow_count'   => $follow_count
        ];

        // ユーザーの投稿一覧取得
        $posts = Post::join('users', 'posts.user_id', '=', 'users.id')
                    ->join('restaurants', 'posts.restaurant_id', '=', 'restaurants.id')
                    ->orderByDesc('posts.id')
                    ->get();
        if ( $posts->isEmpty() ) {
            $response_data['posts'] = [];
        } else {
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
        }

        return [
            'data' => $response_data
        ];
    }

    public function createUser(
        string $user_name,
        string $email,
        string $password,
        int    $gender,
        int    $user_type
    ) {
        $insert_data = [
            'user_name' => $user_name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'gender'    => $gender,
            'user_type' => $user_type
        ];

        if (! User::create($insert_data)) {
            // TODO
            throw new DataOperationException('ユーザー登録エラー');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function updateUser(
        User   $user,
        string $user_name,
        int    $gender
    ) {
        // ユーザーチェック
        $check = Gate::inspect('update', $user);
        if ($check->denied()) {
            throw new UnauthorizationException('不正なユーザー編集');
        }

        $user->user_name = $user_name;
        $user->gender    = $gender;

        if (!$user->save()) {
            throw new DataOperationException('ユーザー編集エラー');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deleteUser(User $user)
    {
        // ユーザーチェック
        $check = Gate::inspect('delete', $user);
        if ($check->denied()) {
            throw new UnauthorizationException('不正なユーザー削除');
        }

        if (!$user->delete()) {
            throw new DataOperationException('ユーザー削除エラー');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function loginUser(string $email, string $password)
    {
        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (! Auth::attempt($credentials)) {
            // TODO
            // 認証エラー
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function logoutUser()
    {
        Auth::guard('web')->logout();

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }
}
