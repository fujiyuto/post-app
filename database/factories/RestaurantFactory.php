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
            'restaurant_name' => fake()->realText(10),
            'zip_cd'          => fake()->postcode(),
            'address'         => fake()->prefecture() . fake()->city(),
            'address_detail'  => fake()->streetAddress(),
            'email'           => fake()->safeEmail(),
            'tel_no'          => fake()->phoneNumber(),
            'price_min'       => 1000,
            'price_max'       => 4000
        ];
    }
}
