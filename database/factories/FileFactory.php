<?php

namespace Database\Factories;

use App\Models\Directory;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
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
            'directory_id' => Directory::factory(),
            'type' => fake()->randomElement(['pdf', 'jpg', 'rar', 'zip', 'doc', 'txt', 'png', 'ppt']),
            'name' => fn (array $attributes) => Str::ucfirst(fake()->words(3, true)).'.'.$attributes['type'],
            'is_owned' => fake()->boolean(),
            'comment' => fake()->optional()->sentence(),
            'size' => fake()->numberBetween(1024, 64000000),
            'total_views' => fake()->numberBetween(0, 6000),
            'total_downloads' => fake()->numberBetween(0, 2000),
            'total_overall' => fn (array $attributes) => $attributes['total_downloads'] + fake()->numberBetween(0, 50),
            'votes_up' => fake()->numberBetween(0, 5),
            'votes_down' => fake()->numberBetween(0, 5),
            'path' => null,
            'md5' => fake()->md5(),
            'user_id' => User::factory(),
            'deleted_by_user_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (File $file) {
            if ($file->path === null) {
                $file->calculatePath();
            }
        });
    }
}
