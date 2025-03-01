<?php

namespace App\Services;

use App\Enums\Status;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantGenre;
use App\Models\RestaurantImage;
use App\Models\RestaurantReservationStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RestaurantService
{
    /**
     * 店一覧を取得する
     *
     * @param string $genre_unique_name
     * @param string $region
     * @param string $keyword
     * @return array
     */
    public function getRestaurants(string $genre_unique_name, string $region, string $keyword)
    {
        $search_data = Restaurant::search("{$keyword} {$region} {$genre_unique_name}")->with(['hitsPerPage' => 30])->get();
        $restaurant_id_list = $search_data->pluck('id');

        // 店に紐づくジャンル取得
        $restaurant_genre_rel = RestaurantGenre::selectRaw('restaurant_genres.restaurant_id, genres.unique_name, genres.genre_name')
                                                ->join('genres', 'restaurant_genres.genre_id', '=', 'genres.id')
                                                ->whereIn('restaurant_genres.restaurant_id', $restaurant_id_list)
                                                ->get();
        $restaurant_genre_map = [];
        foreach ($restaurant_genre_rel as $rel) {
            if (!array_key_exists($rel->restaurant_id, $restaurant_genre_map)) {
                $restaurant_genre_map[$rel->restaurant_id] = [];
            }
            $restaurant_genre_map[$rel->restaurant_id][] = [
                'unique_name' => $rel->unique_name,
                'genre_name'  => $rel->genre_name
            ];
        }

        // サムネ画像を取得
        /**
         * 以下の連想配列
         * [ 店ID => サムネ画像URL, ...]
         */
        $thumbnail_images = RestaurantImage::selectRaw('restaurant_images.restaurant_id, restaurant_images.image_url')
                                            ->whereIn('restaurant_images.restaurant_id', $restaurant_id_list)
                                            ->pluck('restaurant_images.image_url', 'restaurant_images.restaurant_id')
                                            ->toArray();


        // 予約状況テーブルから今日の予約状況を取得
        $now = Carbon::now();
        $restaurant_reservation_status_data = RestaurantReservationStatus::selectRaw('restaurant_id, reserve_date, is_reservable')
                                                                        ->whereIn('restaurant_id', $restaurant_id_list)
                                                                        ->where('reserve_date', $now->format('Y-m-d'))
                                                                        ->get();
        /**
         * どの店が何日に予約可能か連想配列を作成
         * [
         *     1 => [
         *         [
         *             'reserve_date   => '2025-01-01',
         *             'is_reservable' => true
         *         ],
         *         [
         *             'reserve_date   => '2025-01-02',
         *             'is_reservable' => false
         *         ],
         *     ],
         *     2 => [],
         * ]
         */
        $reservable_info_list = [];
        foreach ($restaurant_reservation_status_data as $data) {
            if (!array_key_exists($data->restaurant_id, $reservable_info_list)) {
                $reservable_info_list[$data->restaurant_id] = [
                    'reserve_date'  => $data->reserve_date,
                    'is_reservable' => $data->is_reservable
                ];
            } else {
                $reservable_info_list[$data->restaurant_id][] = [
                    'reserve_date'  => $data->reserve_date,
                    'is_reservable' => $data->is_reservable
                ];
            }
        }

        $response_data = [];
        foreach ($search_data as $restaurant) {
            $update_datetime = new Carbon($restaurant['updated_at']);
            $update_date     = $update_datetime->format('Y-m-d');
            $response_data[] = [
                'id' => $restaurant->id,
                'restaurant_name' => $restaurant->restaurant_name,
                'address'         => $restaurant->address,
                'price_min'       => $restaurant->price_min,
                'price_max'       => $restaurant->price_max,
                'post_num'        => $restaurant->post_num,
                'point_avg'       => $restaurant->point_avg,
                'updated_at'      => $update_date,
                'thumbnail_image' => $thumbnail_images[$restaurant->id],
                'genres'          => $restaurant_genre_map[$restaurant->id],
                'reservable_list' => $reservable_info_list
            ];
        }

        return [
            'restaurants' => $response_data
        ];

    }

    /**
     * 営業開始・終了時間（HH:mm）と日数を受け取り、以下の形式
     * [
     *   'yyyy-mm-dd' => [
     *     'HH:mm' => [
     *       'num_of_people' => 0,
     *       'available' => true
     *     ],...
     *   ]
     * ]
     *
     * @param string $open_time
     * @param string $close_time
     * @return array
     */
    private function makeReservationResponseFrame(string $open_time, string $close_time): array
    {
        $open_time_arr  = explode(':', $open_time);
        $close_time_arr = explode(':', $close_time);
        $open_hour      = (int)$open_time_arr[0];
        $open_minute    = (int)$open_time_arr[1];
        $close_hour     = (int)$close_time_arr[0];
        $close_minute   = (int)$close_time_arr[1];

        $response_arr = [];
        $oh = $open_hour;
        $om = $open_minute;
        while ($oh != $close_hour && $om != $close_minute) {
            if ( $om === 60 ) {
                $oh++;
                $om = 0;
            }
            $response_arr[Str::padLeft((string)$oh, 2, '0') . ':' . Str::padLeft((string)$om, 2, '0')] = [
                'num_of_people' => 0,
                'available'     => true
            ];
            $om += 15;
        }

        return $response_arr;
    }

    /**
     * 店の詳細を取得する
     *
     * @param  Restaurant $restaurant
     * @return array
     */
    public function getRestaurant(Restaurant $restaurant)
    {
        $genres = RestaurantGenre::selectRaw('genres.genre_name, genres.unique_name')
                                    ->join('genres', 'genres.id', '=', 'restaurant_genres.genre_id')
                                    ->where('restaurant_genres.restaurant_id', $restaurant->id)
                                    ->get()->toArray();

        $restaurant_images = RestaurantImage::selectRaw('restaurant_images.image_url, image_categories.unique_cd, image_categories.name')
                                            ->join('image_categories', 'image_categories.id', '=', 'restaurant_images.image_category_id')
                                            ->where('restaurant_images.restaurant_id', $restaurant->id)
                                            ->get();
        $images = [];
        foreach ($restaurant_images as $ri) {
            if (!array_key_exists($ri->unique_cd, $images)) {
                $images[$ri->unique_cd] = [
                    'name' => $ri->name,
                    'image_urls' => []
                ];
            }
            $images[$ri->unique_cd]['image_urls'][] = $ri->image_url;
        }


        $update_datetime = new Carbon($restaurant->updated_at);
        $update_date     = $update_datetime->format('Y-m-d');

        $response_data = [
            'id'              => $restaurant->id,
            'restaurant_name' => $restaurant->restaurant_name,
            'zip_cd'          => $restaurant->zip_cd,
            'address'         => $restaurant->address,
            'address_detail'  => $restaurant->address_detail,
            'email'           => $restaurant->email,
            'tel_no'          => $restaurant->tel_no,
            'price_min'       => $restaurant->price_min,
            'price_max'       => $restaurant->price_max,
            'post_num'        => $restaurant->post_num,
            'point_avg'       => $restaurant->point_avg,
            'updated_at'      => $update_date,
            'images'          => $images,
            'genres'          => $genres
        ];

        return $response_data;
    }

    /**
     * 店情報を登録する
     *
     * @param string $restaurant_name
     * @param string $zip_cd
     * @param string $address
     * @param string|null $email
     * @param string $tel_no
     * @param integer|null $price_min
     * @param integer|null $price_max
     * @return array
     */
    public function createRestaurant(
        string $restaurant_name,
        string $zip_cd,
        string $address,
        string $email = null,
        string $tel_no,
        int $price_min = null,
        int $price_max = null
    ) {
        $insert_data = [
            'restaurant_name' => $restaurant_name,
            'zip_cd'          => $zip_cd,
            'address'         => $address,
            'email'           => $email,
            'tel_no'          => $tel_no,
            'price_min'       => $price_min,
            'price_max'       => $price_max
        ];

        if ( !Restaurant::create($insert_data) ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return ['ok' => true];
    }

    /**
     * 店情報を更新する
     *
     * @param Restaurant $restaurant
     * @param string $restaurant_name
     * @param string $zip_cd
     * @param string $address
     * @param string|null $email
     * @param string $tel_no
     * @param integer|null $price_min
     * @param integer|null $price_max
     * @return array
     */
    public function updateRestaurant(
        Restaurant $restaurant,
        string $restaurant_name,
        string $zip_cd,
        string $address,
        string|null $email,
        string $tel_no,
        int|null $price_min,
        int|null $price_max
    ) {
        $restaurant->restaurant_name = $restaurant_name;
        $restaurant->zip_cd          = $zip_cd;
        $restaurant->address         = $address;
        $restaurant->email           = $email;
        $restaurant->tel_no          = $tel_no;
        $restaurant->price_min       = $price_min;
        $restaurant->price_max       = $price_max;

        if (!$restaurant->save()) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return ['ok' => true];
    }

    /**
     * 店情報を削除する
     *
     * @param Restaurant $restaurant
     * @return array
     */
    public function deleteRestaurant(Restaurant $restaurant)
    {
        if (!$restaurant->delete()) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return ['ok' => true];
    }
}
