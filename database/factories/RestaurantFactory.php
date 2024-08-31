<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_name'      => fake()->realText(10),
            'zip_cd'    => fake()->postcode(),
            'address'   => "東京都近藤市中央区若松町津田1-8-4 コーポ井高101号",
            'email'     => fake()->safeEmail(),
            'tel_no'    => fake()->phoneNumber(),
            'price_min' => 1000,
            'price_max' => 4000
        ];
    }
}
