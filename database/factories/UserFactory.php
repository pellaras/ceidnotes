<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => null,
            'remember_token' => Str::random(10),
            'legacy_id' => fake()->unique()->numberBetween(1, 999999),
            'username' => fake()->unique()->userName(),
            'AM' => (string) fake()->unique()->numberBetween(1000000, 9999999),
            'registration_year' => fake()->numberBetween(2000, 2017),
            'send_results_by_email' => fake()->boolean(),
            'phone_notifications_start' => 10,
            'phone_notifications_end' => 21,
            'password_old' => static::$password ??= Hash::make('password'),
            'is_admin' => false,
        ];
    }
}
