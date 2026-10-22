<?php

namespace Database\Factories;

use App\Models\Edit;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Edit>
 */
class EditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'legacy_id' => fake()->unique()->numberBetween(1, 999999),
            'user_id' => User::factory(),
            'editable_id' => File::factory(),
            'editable_type' => File::class,
            'modified_data' => ['from_legacy' => true, 'name' => fake()->words(2, true)],
        ];
    }
}
