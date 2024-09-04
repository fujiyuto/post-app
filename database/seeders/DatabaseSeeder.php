<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Post;
use App\Models\Follow;
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

        $users = User::factory()->count(10)->create();
        $restaurants = Restaurant::factory()->count(5)->create();
        foreach ($users as $user) {
            foreach ($restaurants as $restaurant) {
                Post::factory()->create([
                    'user_id'       => $user->id,
                    'restaurant_id' => $restaurant->id
                ]);
            }
        }

        foreach ($users as $follow) {
            foreach ($users as $follower) {
                if ($follow->id === $follower->id) continue;
                Follow::create([
                    'follow_id' => $follow->id,
                    'follower_id' => $follower->id
                ]);
            }
        }

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

    }
}
