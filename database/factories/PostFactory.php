<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'          => fake()->realTextBetween(10,20),
            'content'        => fake()->realText(150),
            'visited_at'     => fake()->date(),
            'period_of_time' => fake()->numberBetween(1, 2),
            'points'         => fake()->randomFloat(1, 0, 5),
            'price_min'      => 1000,
            'price_max'      => 4000
        ];
    }
}
