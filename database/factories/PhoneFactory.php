<?php

namespace Database\Factories;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Phone>
 */
class PhoneFactory extends Factory
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
            'phone_number' => fake()->unique()->e164PhoneNumber(),
            'verification_code' => Str::upper(fake()->unique()->bothify('????####')),
            'verified' => fake()->boolean(),
        ];
    }
}
