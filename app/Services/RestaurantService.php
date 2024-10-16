<?php

namespace App\Services;

use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataOperationException;
use App\Models\Restaurant;
use App\Models\Genre;
use App\Models\RestaurantGenre;
use Carbon\Carbon;
use App\Models\Tweet;
use Algolia\AlgoliaSearch\SearchIndex;
use Illuminate\Support\Facades\Log;

class RestaurantService {

    public function getRestaurants(string $genre_unique_name, string $region, string $keyword)
    {
        // if ( $genre_cd ) {

        //     $genre = Genre::where('unique_cd', $genre_cd)->first();
        //     if ( !$genre ) {
        //         throw new DataNotFoundException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        //     }

        //     $restaurants = RestaurantGenre::selectRaw('restaurants.id as restaurant_id, restaurant_name, address, price_min, price_max, genre_name, post_num, point_avg, restaurants.updated_at')
        //                                     ->join('restaurants', 'restaurants.id', '=', 'restaurant_genres.restaurant_id')
        //                                     ->join('genres', 'genres.id', '=', 'restaurant_genres.genre_id')
        //                                     ->where('genres.id', $genre->id)
        //                                     ->orderBy('restaurants.updated_at', 'desc')
        //                                     ->get();
        // } else {

        //     $restaurants = Restaurant::selectRaw('id as restaurant_id, restaurant_name, address, price_min, price_max, post_num, point_avg, updated_at')
        //                                 ->orderBy('restaurants.updated_at', 'desc')
        //                                 ->get();
        // }

        // $genres = RestaurantGenre::selectRaw('restaurant_genres.restaurant_id, genres.unique_cd, genres.genre_name')
        //                             ->join('genres', 'restaurant_genres.genre_id', '=', 'genres.id')
        //                             ->get();

        // $restaurant_genre_dict = [];
        // foreach ($genres as $genre) {
        //     $restaurant_genre_dict[$genre->restaurant_id][] = [
        //         'unique_cd' => $genre->unique_cd,
        //         'name'      => $genre->genre_name
        //     ];
        // }

        // $response_data = [];
        // foreach ($restaurants as $restaurant) {

        //     $response_data[] = [
        //         'restaurant' => [
        //             'id'         => $restaurant->restaurant_id,
        //             'name'       => $restaurant->restaurant_name,
        //             'address'    => $restaurant->address,
        //             'price_min'  => $restaurant->price_min,
        //             'price_max'  => $restaurant->price_max,
        //             'post_num'   => $restaurant->post_num,
        //             'point_avg'  => $restaurant->point_avg,
        //             'updated_at' => $update_date,
        //         ],
        //         'genres' => array_key_exists($restaurant->restaurant_id, $restaurant_genre_dict) ?$restaurant_genre_dict[$restaurant->restaurant_id] : []
        //     ];
        // }

        // print_r($genre_names);

        $search_data = Restaurant::search("{$keyword} {$region} {$genre_unique_name}")->with(['hitsPerPage' => 30])->get();
        $restaurant_id_list = $search_data->pluck('id');
        $resraurant_genre_rel = RestaurantGenre::selectRaw('restaurant_genres.restaurant_id, genres.unique_name, genres.genre_name')
                                                ->join('genres', 'restaurant_genres.genre_id', '=', 'genres.id')
                                                ->whereIn('restaurant_genres.restaurant_id', $restaurant_id_list)
                                                ->get();

        $restaurant_genre_map = [];
        foreach ($resraurant_genre_rel as $rel) {
            if ( !array_key_exists($rel->restaurant_id, $restaurant_genre_map) ) {
                $restaurant_genre_map[$rel->restaurant_id] = [];
            }
            $restaurant_genre_map[$rel->restaurant_id][] = [
                'unique_name' => $rel->unique_name,
                'genre_name'  => $rel->genre_name
            ];
        }

        $response_data = [];
        foreach($search_data as $restaurant) {
            $update_datetime = new Carbon($restaurant['updated_at']);
            $update_date     = $update_datetime->format('Y-m-d');
            $response_data[] = [
                'id' => $restaurant->id,
                'restaurant_name' => $restaurant->restaurant_name,
                'address' => $restaurant->address,
                'price_min' => $restaurant->price_min,
                'price_max' => $restaurant->price_max,
                'post_num'  => $restaurant->post_num,
                'point_avg' => $restaurant->point_avg,
                'updated_at' => $update_date,
                'genres' => $restaurant_genre_map[$restaurant->id]
            ];
        }

        return [
            // 'restaurants' => $restaurants
            'restaurants' => $response_data
        ];
    }

    public function getRestaurant(Restaurant $restaurant)
    {
        $genres = RestaurantGenre::selectRaw('genres.genre_name')
                                    ->join('genres', 'genres.id', '=', 'restaurant_genres.genre_id')
                                    ->where('restaurant_genres.restaurant_id', $restaurant->id)
                                    ->pluck('genres.genre_name');

        $update_datetime = new Carbon($restaurant->updated_at);
        $update_date     = $update_datetime->format('Y-m-d');

        $response_data = [
            'restaurant' => [
                'id'         => $restaurant->id,
                'name'       => $restaurant->restaurant_name,
                'zip_cd'     => $restaurant->zip_cd,
                'address'    => $restaurant->address,
                'email'      => $restaurant->email,
                'tel_no'     => $restaurant->tel_no,
                'price_min'  => $restaurant->price_min,
                'price_max'  => $restaurant->price_max,
                'post_num'   => $restaurant->post_num,
                'point_avg'  => $restaurant->point_avg,
                'updated_at' => $update_date
            ],
            'genres' => $genres
        ];

        return [
            'data' => $response_data
        ];
    }

    public function createRestaurant(
        string $restaurant_name,
        string $zip_cd,
        string $address,
        string $email=null,
        string $tel_no,
        int $price_min=null,
        int $price_max=null
    )
    {
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

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function updateRestaurant(
        Restaurant $restaurant,
        string $restaurant_name,
        string $zip_cd,
        string $address,
        string|null $email,
        string $tel_no,
        int|null $price_min,
        int|null $price_max
    )
    {
        $restaurant->restaurant_name = $restaurant_name;
        $restaurant->zip_cd          = $zip_cd;
        $restaurant->address         = $address;
        $restaurant->email           = $email;
        $restaurant->tel_no          = $tel_no;
        $restaurant->price_min       = $price_min;
        $restaurant->price_max       = $price_max;

        if ( !$restaurant->save() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }

    public function deleteRestaurant(Restaurant $restaurant)
    {
        if ( !$restaurant->delete() ) {
            throw new DataOperationException('ERROR: Exception occur in '.__LINE__.' lines of '.basename(__CLASS__));
        }

        return [
            'data' => [
                'ok' => true
            ]
        ];
    }
}
