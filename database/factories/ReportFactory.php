<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
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
            'email' => null,
            'reportable_id' => File::factory(),
            'reportable_type' => File::class,
            'reason' => fake()->sentence(),
        ];
    }
}
