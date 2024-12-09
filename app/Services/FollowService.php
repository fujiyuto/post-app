<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Models\Follow;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class FollowService {
    public function getFollowUsers(User $user, string|null $keyword)
    {
        $response_data = [];

        // ユーザーデータの格納
        $response_data['user'] = [
            'name'   => $user->user_name,
            'gender' => $user->gender
        ];

        $follow_users = Follow::join('users', 'follows.follow_id', '=', 'users.id')
                            ->where('users.id', $user->id)
                            ->get();

        // フォローしているユーザー達のIDの配列を取得
        $follow_id_list = $follow_users->pluck('follower_id')->all();

        // フォローしているユーザー達の名前と投稿数をキー値ペアで取得
        if ( $keyword ) {
            $follow_users = User::whereIn('id', $follow_id_list)
                                ->where('user_name', 'LIKE', "%{$keyword}%")
                                ->get();
            $follow_id_list = $follow_users->pluck('id')->all();
        } else {
            $follow_users = User::whereIn('id', $follow_id_list)->get();
        }
        $follow_users_dict = [];
        foreach ($follow_users as $follow_user) {
            $follow_users_dict[$follow_user->id] = [
                'user_name' => $follow_user->user_name,
                'post_num'  => $follow_user->post_num
            ];
        }

        // フォローしているユーザー達のフォロワー数をキー値ペアで取得
        // キー: フォローしているユーザーのユーザーID, 値: フォロワーのIDのリスト
        $followers_list = Follow::whereIn('follower_id', $follow_id_list)
                                ->get();
        $followers_dict = [];
        foreach ($followers_list as $follower) {
            $followers_dict[$follower->follower_id][] = $follower->follow_id;
        }

        // フォローユーザー
        foreach ($follow_id_list as $id) {
            $response_data['follows'][] = [
                'user_id'      => $id,
                'user_name'    => $follow_users_dict[$id]['user_name'],
                'follower_num' => count($followers_dict[$id]),
                'post_num'     => $follow_users_dict[$id]['post_num'],
                'is_follow'    => in_array(Auth::id(), $followers_dict[$id])
            ];
        }

        return $response_data;
    }

    public function getFollowers(User $user, string|null $keyword)
    {
        $response_data = [];

        // ユーザーデータの格納
        $response_data['user'] = [
            'name'   => $user->user_name,
            'gender' => $user->gender
        ];

        // 取得したいユーザーのIDとfollows.follower_idでfollowersとusersを内部結合
        $follower_users = Follow::join('users', 'follows.follower_id', '=', 'users.id')
                            ->where('users.id', $user->id)
                            ->get();

        // フォロワーIDの配列を取得
        $follower_id_list = $follower_users->pluck('follow_id')->all();

        // フォロワーの名前をキー値ペアで取得
        if ( $keyword ) {
            $follower_users = User::whereIn('id', $follower_id_list)
                                    ->where('user_name', 'LIKE', "%{$keyword}%")
                                    ->get();
            $follower_id_list = $follower_users->pluck('id')->all();
        } else {
            $follower_users = User::whereIn('id', $follower_id_list)
                                    ->where('user_name', 'LIKE', "%{$keyword}%")
                                    ->get();
        }
        $follower_users_dict = [];
        foreach ($follower_users as $follower_user) {
            $follower_users_dict[$follower_user->id] = [
                'user_name' => $follower_user->user_name,
                'post_num'  => $follower_user->post_num
            ];
        }

        // フォロワー達のフォロワー数をキー値ペアで取得
        // キー: フォロワーのユーザーID, 値: フォロワー数
        $followers_list = Follow::whereIn('follower_id', $follower_id_list)
                                    ->get();
        $followers_dict = [];
        foreach ($followers_list as $follower) {
            $followers_dict[$follower->follower_id][] = $follower->follow_id;
        }

        // フォロワー
        foreach ($follower_id_list as $follower_id) {
            $response_data['followers'][] = [
                'user_id'      => $follower_id,
                'user_name'    => $follower_users_dict[$follower_id]['user_name'],
                'follower_num' => count($followers_dict[$follower_id]),
                'post_num'     => $follower_users_dict[$follower_id]['post_num'],
                'is_follow'    => in_array(Auth::id(), $followers_dict[$follower_id])
            ];
        }

        return $response_data;
    }

    public function createFollows(int $follow_id, int $follower_id)
    {
        $insert_data = [
            'follow_id'   => $follow_id,
            'follower_id' => $follower_id
        ];
        if ( !Follow::create($insert_data) ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        // フォローした人のフォロー数、フォローされた人のフォロワー数の値を更新
        $users = User::whereIn('id', [$follow_id, $follower_id])->get();
        foreach ($users as $user) {
            if ( $user->id === $follow_id ) {
                $user->follow_num++;
            } else {
                $user->follower_num++;
            }

            if ( !$user->save() ) {
                throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
            }
        }

        return ['ok' => true];
    }

    public function deleteFollows(int $follow_id, int $follower_id)
    {
        $follow = Follow::where([
                            'follow_id' => $follow_id,
                            'follower_id' => $follower_id
                        ])->first();

        // フォロー関係なし
        if ( !$follow ) {
            throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        if ( !$follow->delete() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return ['ok' => true];
    }
}
