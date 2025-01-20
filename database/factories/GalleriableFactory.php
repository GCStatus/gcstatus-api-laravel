<?php

namespace Database\Factories;

use App\Models\MediaType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Galleriable>
 */
class GalleriableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            's3' => false,
            'path' => fake()->imageUrl(),
            'media_type_id' => fake()->randomElement([MediaType::PHOTO_CONST_ID, MediaType::VIDEO_CONST_ID]),
        ];
    }
}
