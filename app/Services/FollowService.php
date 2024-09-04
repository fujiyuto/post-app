<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Models\Follow;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class FollowService {
    public function getFollowUsers(User $user)
    {
        $response_data = [];

        // ユーザーデータの格納
        $response_data['user'] = [
            'user_name' => $user->user_name,
            'gender'    => $user->gender
        ];

        // 取得したいユーザーのIDとfollows.follower_idでfollowersとusersを内部結合
        $follow_users = Follow::join('users', 'follows.follow_id', '=', 'users.id')
                            ->where('users.id', $user->id)
                            ->get();

        // フォローしているユーザー達のIDの配列を取得
        $follow_id_list = $follow_users->pluck('follower_id')->all();

        // フォローしているユーザー達の名前をキー値ペアで取得
        $follow_users = User::whereIn('id', $follow_id_list)->get();
        $follow_users_dict = [];
        foreach ($follow_users as $follow_user) {
            $follow_users_dict[$follow_user->id] = $follow_user->user_name;
        }

        // フォローしているユーザー達のフォロワー数をキー値ペアで取得
        // キー: フォローしているユーザーのユーザーID, 値: フォロワーのIDのリスト
        $followers_list = Follow::whereIn('follower_id', $follow_id_list)
                                ->get();
        $followers_dict = [];
        foreach ($followers_list as $follower) {
            $followers_dict[$follower->follower_id][] = $follower->follow_id;
        }

        // フォローしているユーザーの投稿数をキー値ペアで取得
        // キー: フォローしているユーザーのユーザーID, 値: 投稿数
        $follows_count_post_dict = Post::selectRaw('count(id) as post_num, user_id')
                                        ->whereIn('user_id', $follow_id_list)
                                        ->groupBy('user_id')
                                        ->pluck('post_num', 'user_id');

        // フォローユーザー
        foreach ($follow_id_list as $id) {
            $response_data['follows'][] = [
                'user_name'    => $follow_users_dict[$id],
                'follower_num' => count($followers_dict[$id]),
                'post_num'     => $follows_count_post_dict[$id],
                'is_follow'    => in_array(Auth::id(), $followers_dict[$id])
            ];
        }

        return [
            'data' => $response_data
        ];
    }

    public function getFollowers(User $user)
    {
        $response_data = [];

        // ユーザーデータの格納
        $response_data['user'] = [
            'user_name' => $user->user_name,
            'gender'    => $user->gender
        ];

        // 取得したいユーザーのIDとfollows.follower_idでfollowersとusersを内部結合
        $follower_users = Follow::join('users', 'follows.follower_id', '=', 'users.id')
                            ->where('users.id', $user->id)
                            ->get();

        // フォロワーIDの配列を取得
        $follower_id_list = $follower_users->pluck('follow_id')->all();

        // フォロワーの名前をキー値ペアで取得
        $follower_users = User::whereIn('id', $follower_id_list)->get();
        $follower_users_dict = [];
        foreach ($follower_id_list as $follower_id) {
            $follower_users_dict[$follower_id] = [];
        }
        foreach ($follower_users as $follower_user) {
            $follower_users_dict[$follower_user->id] = $follower_user->user_name;
        }

        // フォロワー達のフォロワー数をキー値ペアで取得
        // キー: フォロワーのユーザーID, 値: フォロワー数
        $followers_list = Follow::whereIn('follower_id', $follower_id_list)
                                    ->get();
        $followers_dict = [];
        foreach ($followers_list as $follower) {
            $followers_dict[$follower->follower_id][] = $follower->follow_id;
        }

        // フォロワーの投稿数をキー値ペアで取得
        // キー: フォロワーのユーザーID, 値: 投稿数
        $followers_count_post_dict = Post::selectRaw('count(id) as post_num, user_id')
                                        ->whereIn('user_id', $follower_id_list)
                                        ->groupBy('user_id')
                                        ->pluck('post_num', 'user_id');

        // フォロワー
        foreach ($follower_id_list as $follower_id) {
            $response_data['followers'][] = [
                'user_name'    => $follower_users_dict[$follower_id],
                'follower_num' => count($followers_dict[$follower_id]),
                'post_num'     => $followers_count_post_dict[$follower_id]
            ];
        }

        return [
            'data' => $response_data
        ];
    }

    public function createFollows(int $follow_id, int $follower_id)
    {
        $insert_data = [
            'follow_id'   => $follow_id,
            'follower_id' => $follower_id
        ];
        if ( !Follow::create($insert_data) ) {
            throw new DataOperationException('ユーザーフォロー失敗');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deleteFollows(int $follow_id, int $follower_id)
    {
        $follow = Follow::where([
                            'follow_id' => $follow_id,
                            'follower_id' => $follower_id
                        ])->first();

        // フォロー関係なし
        if ( !$follow ) {
            throw new DataNotFoundException('フォロー関係はありません');
        }

        if ( !$follow->delete() ) {
            throw new DataOperationException('ユーザーアンフォロー失敗');
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }
}
