<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->getOrCreateUser(),
            'skin_type' => fake()->randomElement(UserProfile::VALID_SKIN_TYPES),
            'primary_skin_concerns' => fake()->randomElement(UserProfile::VALID_SKIN_CONCERNS),
            'secondary_skin_concerns' => fake()->randomElements(
                UserProfile::VALID_SKIN_CONCERNS, 
                fake()->numberBetween(1, 3)
            ),
            'allergies' => fake()->randomElements([
                'nuts', 'dairy', 'gluten', 'fragrances', 'sulfates', 'parabens'
            ], fake()->numberBetween(0, 2)),
            'environmental_factors' => fake()->randomElement(UserProfile::VALID_ENVIRONMENTAL_FACTORS),
        ];
    }

    /**
     * Get existing User or create one
     */
    private function getOrCreateUser()
    {
        // Try to find a user without a profile
        $userWithoutProfile = User::doesntHave('profile')->first();
        
        if ($userWithoutProfile) {
            return $userWithoutProfile->user_id;
        }

        // Create a new user
        $user = User::factory()->create();
        return $user->user_id;
    }

    public function dry(): static
    {
        return $this->state(['skin_type' => 'dry']);
    }

    public function oily(): static
    {
        return $this->state(['skin_type' => 'oily']);
    }

    public function sensitive(): static
    {
        return $this->state(['skin_type' => 'sensitive']);
    }

    public function withAllergies(array $allergies): static
    {
        return $this->state(['allergies' => $allergies]);
    }

    public function complete(): static
    {
        return $this->state([
            'secondary_skin_concerns' => fake()->randomElements(UserProfile::VALID_SKIN_CONCERNS, 2),
            'allergies' => fake()->randomElements(['nuts', 'dairy'], 1),
        ]);
    }

    public function forUser($userId): static
    {
        return $this->state(['user_id' => $userId]);
    }
}