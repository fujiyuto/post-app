<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Post;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Genre;
use App\Models\RestaurantGenre;
use Database\Factories\UserFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Restaurant::factory()
        //         ->count(10)
        //         ->hasPosts(3, [
        //             'user_id' => User::factory()
        //         ])
        //         ->create();

        // ユーザー作成
        $users = User::factory()->count(10)->create();
        // 店作成
        $restaurants = Restaurant::factory()->count(5)->create();
        // 投稿作成
        $posts = [];
        foreach ($users as $user) {
            foreach ($restaurants as $restaurant) {
                $post = Post::factory()->create([
                    'user_id'       => $user->id,
                    'restaurant_id' => $restaurant->id
                ]);
                $restaurant->point_avg = round((($restaurant->point_avg * $restaurant->post_num) + $post->points) / ($restaurant->post_num + 1), 1);
                $restaurant->post_num++;
                $restaurant->save();
                $posts[] = $post;
            }
        }
        // フォロー作成
        foreach ($users as $follow) {
            foreach ($users as $follower) {
                if ($follow->id === $follower->id) continue;
                Follow::create([
                    'follow_id' => $follow->id,
                    'follower_id' => $follower->id
                ]);
            }
        }
        // いいね作成
        foreach ($users as $user) {
            foreach ($posts as $post) {
                if ( $user->id === $post->user_id ) continue;
                Like::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id
                ]);
            }
        }
        // ジャンル作成
        $genres = [];
        for ($i = 1; $i <= 10; $i++) {
            $genres[] = Genre::create([
                'unique_cd' => str_pad((string)$i, 4, 0, STR_PAD_LEFT),
                'genre_name' => 'テストジャンル'.$i
            ]);
        }

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

    }
}
