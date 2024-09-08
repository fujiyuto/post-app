<?php

namespace App\Services;

use App\Exceptions\DataOperationException;
use App\Exceptions\UnauthorizationException;
use App\Models\User;
use App\Models\UserStoreRestaurant;
use App\Models\RestaurantGenre;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class UserStoreRestaurantService {

    public function getUserStoreRestaurant(User $user)
    {
        // ユーザー保存店から店IDの配列を取得
        $restaurant_id_list = UserStoreRestaurant::where('user_id', $user->id)->pluck('restaurant_id');

        // 店ジャンルと店テーブルを内部結合し、結合したテーブルから取得した店IDの配列に店IDが存在するデータを取得
        $restaurants = RestaurantGenre::selectRaw('restaurants.id as restaurant_id, restaurant_name, address, price_min, price_max, post_num, point_avg, restaurants.updated_at')
        ->join('restaurants', 'restaurants.id', '=', 'restaurant_genres.restaurant_id')
        ->whereIn('restaurant_id', $restaurant_id_list)
        ->orderBy('restaurants.updated_at', 'desc')
        ->get();

        // 店ジャンルとジャンルテーブルを内部結合し、結合したテーブルから取得した店IDの配列に店IDが存在するデータを取得
        $genres = RestaurantGenre::selectRaw('restaurant_genres.restaurant_id, genres.unique_cd, genres.genre_name')
                                    ->join('genres', 'genres.id', '=', 'restaurant_genres.genre_id')
                                    ->whereIn('restaurant_genres.restaurant_id', $restaurant_id_list)
                                    ->get();

        /**
         * 下記のような連想配列を設定
         * [
         *    "店ID1" => [
         *       ["ジャンルユニークコード1", "ジャンル名1"],
         *       ["ジャンルユニークコード2", "ジャンル名2"],
         *       ...
         *    ],
         *    "店ID2" => [
         *       ["ジャンルユニークコード3", "ジャンル名3"],
         *       ["ジャンルユニークコード4", "ジャンル名4"],
         *       ...
         *    ],
         *    ...
         * ]
         */
        $restaurant_genre_dict = [];
        foreach ($genres as $genre) {
            $restaurant_genre_dict[$genre->restaurant_id][] = [
                'unique_cd' => $genre->unique_cd,
                'name'      => $genre->genre_name
            ];
        }

        // レスポンスデータを整形
        $response_data = [];
        foreach ($restaurants as $restaurant) {
            $update_datetime = new Carbon($restaurant->updated_at);
            $update_date     = $update_datetime->format('Y-m-d');
            $response_data[] = [
                'restaurant' => [
                    'id'         => $restaurant->restaurant_id,
                    'name'       => $restaurant->restaurant_name,
                    'address'    => $restaurant->address,
                    'price_min'  => $restaurant->price_min,
                    'price_max'  => $restaurant->price_max,
                    'post_num'   => $restaurant->post_num,
                    'point_avg'  => $restaurant->point_avg,
                    'updated_at' => $update_date,
                ],
                'genres' => $restaurant_genre_dict[$restaurant->restaurant_id]
            ];
        }

        return [
            'data' => [
                'restaurants' => $response_data
            ]
        ];
    }

    public function createUserStoreRestaurant(int $user_id, int $restaurant_id)
    {

        $insert_data = [
            'user_id'       => $user_id,
            'restaurant_id' => $restaurant_id
        ];

        // 重複データがある場合は削除してから作成
        $check_exist = UserStoreRestaurant::where($insert_data)->get();
        if ( !$check_exist->isEmpty() ) {
            // 削除件数が0なら削除できていないのでエラー
            if ( UserStoreRestaurant::destroy($check_exist->pluck('id')) == 0 ) {
                throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
            }
        }

        if ( !UserStoreRestaurant::create($insert_data) ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deleteUserStoreRestaurant(int $user_id, int $restaurant_id)
    {
        $delete_data = UserStoreRestaurant::where([
            'user_id'       => $user_id,
            'restaurant_id' => $restaurant_id
        ])->first();

        // ユーザーチェック
        $check = Gate::inspect('delete', $delete_data);
        if ( $check->denied() ) {
            throw new UnauthorizationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }


        if ( !$delete_data->delete() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }
}
