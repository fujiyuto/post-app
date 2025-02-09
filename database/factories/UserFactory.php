<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_name'         => fake()->unique()->userName(),
            'email'             => fake()->unique()->safeEmail(),
            'tel_no'            => fake()->phoneNumber(),
            'birthday'          => fake()->date(),
            'email_verified_at' => now(),
            'password'          => Hash::make(static::$password ??= 'password'),
            'gender'            => fake()->numberBetween(1, 2),
            'user_type'         => 1
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
