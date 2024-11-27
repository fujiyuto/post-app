<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Post;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Genre;
use App\Models\GenreGroup;
use App\Models\ImageCategory;
use App\Models\RestaurantGenre;
use App\Models\RestaurantImage;
use Database\Factories\UserFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // ユーザー作成
        $users = User::factory()->count(10)->create();
        // 店作成
        $restaurants = Restaurant::factory()->count(50)->create();
        // 投稿作成
        $posts = [];
        foreach ($users as $user) {
            foreach ($restaurants as $restaurant) {
                $post = Post::factory()->create([
                    'user_id'       => $user->id,
                    'restaurant_id' => $restaurant->id
                ]);

                $user->post_num++;
                $user->save();

                $restaurant->point_avg = round((($restaurant->point_avg * $restaurant->post_num) + $post->points) / ($restaurant->post_num + 1), 1);
                $restaurant->post_num++;
                $restaurant->save();
                $posts[] = $post;
            }
        }
        // フォロー作成
        $follower_map = [];
        $follow_map = [];
        foreach ($users as $follower) {

            if ( !array_key_exists($follower->id, $follower_map) ) {
                $follower_map[$follower->id] = [];
            }

            foreach ($users as $follow) {

                if ( !array_key_exists($follow->id, $follow_map) ) {
                    $follow_map[$follow->id] = [];
                }

                if ($follow->id === $follower->id) continue;
                $f = Follow::create([
                    'follow_id' => $follow->id,
                    'follower_id' => $follower->id
                ]);
                $follower_map[$follower->id][] = $follow->id;
                $follow_map[$follow->id][] = $follower->id;
            }
        }

        // いいね作成 & ユーザーの
        foreach ($users as $user) {
            $user->follower_num = count($follower_map[$user->id]);
            $user->follow_num = count($follow_map[$user->id]);
            $user->save();
            foreach ($posts as $post) {
                if ( $user->id === $post->user_id ) continue;
                Like::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id
                ]);
            }
        }

        // ジャンルグループ作成
        $genre_groups = GenreGroup::factory()->count(5)->create();
        // ジャンル作成
        $genres = [];
        foreach ($genre_groups as $group) {
            $created_genres = Genre::factory()->count(10)->create(['genre_group_id' => $group->id])->toArray();
            $genres = array_merge($genres, $created_genres);
        }


        // 店とジャンルの関連付け
        foreach ($restaurants as $restaurant) {
            RestaurantGenre::create([
                'restaurant_id' => $restaurant->id,
                'genre_id' => $genres[rand(0,49)]['id']
            ]);
        }

        // 店の画像についてのデータ
        $image_category_arr = [
            '1000' => '料理',
            '1100' => 'メニュー',
            '1200' => '外観',
            '1300' => '店内'
        ];
        $image_categories = [];
        foreach ($image_category_arr as $key => $name) {
            $image_categories[] = ImageCategory::create([
                'unique_cd' => $key,
                'name'      => $name
            ]);
        }
        foreach ($image_categories as $ic) {
            foreach ($restaurants as $restaurant) {
                RestaurantImage::create([
                    'restaurant_id' => $restaurant->id,
                    'image_category_id' => $ic->id,
                    'image_url' => 'https://placehold.jp/300*200.png',
                    'is_thumbnail' => $ic->id == 1 ? 1 : 0
                ]);
            }
        }
    }
}
