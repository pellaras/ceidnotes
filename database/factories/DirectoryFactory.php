<?php

namespace Database\Factories;

use App\Models\Directory;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Directory>
 */
class DirectoryFactory extends Factory
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
            'directory_id' => fn () => Lesson::factory()->create()->directory_id,
            'name' => Str::ucfirst(fake()->words(2, true)),
            'path' => null,
            'user_id' => User::factory(),
            'deleted_by_user_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Directory $directory) {
            if ($directory->path === null) {
                $directory->calculatePath();
            }
        });
    }

    public function root(): static
    {
        return $this->state([
            'directory_id' => null,
        ]);
    }
}
