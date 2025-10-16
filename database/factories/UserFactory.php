<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'phone_number' => fake()->unique()->e164PhoneNumber(),
            'otp_verified_at' => now(),
            'otp_attempts' => 0,
            'password' => static::$password ??= 'password',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'otp_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model is currently locked from requesting OTPs.
     */
    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'otp_locked_until' => now()->addHour(),
        ]);
    }
}
