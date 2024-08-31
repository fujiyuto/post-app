<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
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
        Restaurant::factory()
                ->count(10)
                ->hasPosts(3, [
                    'user_id' => User::factory()
                ])
                ->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

    }
}
