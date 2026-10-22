<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'legacy_id' => fake()->unique()->numerify('####-####'),
            'user_id' => User::factory(),
            'likeable_id' => File::factory(),
            'likeable_type' => File::class,
            'value' => fake()->randomElement([1, -1]),
        ];
    }
}
