<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

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
            // Legacy database fields
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'password_hash' => static::$password ??= Hash::make('password'),
            'registration_date' => now(),
            'last_login' => null,
            'account_status' => 1, // Active by default
            'role' => 'user',
            
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
        ]);
    }

    /**
     * Create an inactive user.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_status' => 0,
        ]);
    }

    /**
     * Create a user with recent login.
     */
    public function recentLogin(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_login' => now()->subHours(rand(1, 24)),
        ]);
    }

    /**
     * Create a user with specific role.
     */
    public function role(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
        ]);
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->first_name . ' ' . $user->last_name . '\'s Team',
                    'user_id' => $user->user_id, // Use correct primary key
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }

    /**
     * Create a user with a complete profile for testing.
     */
    public function withProfile(): static
    {
        return $this->afterCreating(function (User $user) {
            // Create a user profile if UserProfile model exists
            if (class_exists(\App\Models\UserProfile::class)) {
                \App\Models\UserProfile::factory()->create([
                    'user_id' => $user->user_id,
                ]);
            }
        });
    }

    /**
     * Create a user with orders for testing.
     */
    public function withOrders(int $count = 3): static
    {
        return $this->afterCreating(function (User $user) use ($count) {
            if (class_exists(\App\Models\Order::class)) {
                \App\Models\Order::factory()
                    ->count($count)
                    ->create(['user_id' => $user->user_id]);
            }
        });
    }

    /**
     * Create a user with custom products for testing.
     */
    public function withCustomProducts(int $count = 2): static
    {
        return $this->afterCreating(function (User $user) use ($count) {
            if (class_exists(\App\Models\CustomProduct::class)) {
                \App\Models\CustomProduct::factory()
                    ->count($count)
                    ->create(['user_id' => $user->user_id]);
            }
        });
    }
}