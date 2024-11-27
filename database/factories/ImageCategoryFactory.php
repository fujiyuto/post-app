<?php

namespace Database\Factories;

use App\Models\ImageCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImageCategory>
 */
class ImageCategoryFactory extends Factory
{
    protected static $unique_num = 10;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unique_cd' => self::$unique_num
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (ImageCategory $im) {
            self::$unique_num += 10;
        });
    }
}
