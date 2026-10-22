<?php

namespace Database\Factories;

use App\Models\Directory;
use App\Models\Lesson;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
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
            'KM' => Str::upper(fake()->unique()->bothify('??###')),
            'name' => Str::title(fake()->words(2, true)),
            'category' => fake()->randomElement(['Υποχρεωτικό', 'Επιλογής']),
            'semester_id' => Semester::factory(),
            'directory_id' => fn (array $attributes) => Directory::factory()->root()->state(['name' => $attributes['name']]),
        ];
    }
}
