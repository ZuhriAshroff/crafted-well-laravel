<?php

namespace Database\Factories;

use App\Models\CustomProduct;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomProductFactory extends Factory
{
    protected $model = CustomProduct::class;

    public function definition(): array
    {
        $skinTypes = ['dry', 'oily', 'combination', 'sensitive'];
        $concerns = ['blemish', 'wrinkle', 'spots', 'soothe'];
        $environments = ['urban', 'tropical', 'moderate'];
        $allergies = array_keys(CustomProduct::ALLERGY_CATEGORIES);

        $selectedConcerns = fake()->randomElements($concerns, fake()->numberBetween(1, 3));
        $selectedAllergies = fake()->boolean(30) ? fake()->randomElements($allergies, fake()->numberBetween(1, 2)) : [];

        $profileData = [
            'skin_type' => fake()->randomElement($skinTypes),
            'skin_concerns' => $selectedConcerns,
            'environmental_factors' => fake()->randomElement($environments),
            'allergies' => $selectedAllergies,
        ];

        // Generate composition based on profile
        $composition = CustomProduct::generateProductComposition($profileData);
        $productName = CustomProduct::generateProductName($profileData);
        $productDescription = CustomProduct::generateProductDescription($profileData, $composition);
        $totalPrice = CustomProduct::calculateTotalPrice($composition['ingredients']);

        return [
            'user_id' => User::factory(),
            'base_product_id' => Product::factory(),
            'profile_data' => $profileData,
            'total_price' => $totalPrice,
            'selected_ingredients' => $composition['ingredients'],
            'final_ingredient_concentrations' => $composition['concentrations'],
            'product_name' => $productName,
            'product_description' => $productDescription,
            'formulation_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function forUser($userId): static
    {
        return $this->state(['user_id' => $userId]);
    }

    public function drySkin(): static
    {
        return $this->state(function (array $attributes) {
            $profileData = $attributes['profile_data'];
            $profileData['skin_type'] = 'dry';
            
            $composition = CustomProduct::generateProductComposition($profileData);
            
            return [
                'profile_data' => $profileData,
                'selected_ingredients' => $composition['ingredients'],
                'final_ingredient_concentrations' => $composition['concentrations'],
                'total_price' => CustomProduct::calculateTotalPrice($composition['ingredients']),
            ];
        });
    }

    public function withAllergies(array $allergies): static
    {
        return $this->state(function (array $attributes) use ($allergies) {
            $profileData = $attributes['profile_data'];
            $profileData['allergies'] = $allergies;
            
            $composition = CustomProduct::generateProductComposition($profileData);
            
            return [
                'profile_data' => $profileData,
                'selected_ingredients' => $composition['ingredients'],
                'final_ingredient_concentrations' => $composition['concentrations'],
                'total_price' => CustomProduct::calculateTotalPrice($composition['ingredients']),
            ];
        });
    }
}