<?php

namespace Database\Factories;

use App\Models\BaseFormulation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BaseFormulationFactory extends Factory
{
    protected $model = BaseFormulation::class;

    public function definition(): array
    {
        $categories = array_keys(BaseFormulation::FORMULATION_CATEGORIES);
        $skinTypes = BaseFormulation::SKIN_TYPES;
        $universalIngredients = array_keys(BaseFormulation::UNIVERSAL_INGREDIENTS);

        // Select random ingredients (3-6 ingredients)
        $selectedIngredients = fake()->randomElements($universalIngredients, fake()->numberBetween(3, 6));
        
        // Generate concentration ranges for selected ingredients
        $concentrationRanges = [];
        foreach ($selectedIngredients as $ingredient) {
            if (isset(BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient])) {
                $baseRange = BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient]['concentration_range'];
                // Add some variation to the base ranges
                $concentrationRanges[$ingredient] = [
                    'min' => max(0, $baseRange['min'] - fake()->randomFloat(2, 0, 1)),
                    'max' => min(100, $baseRange['max'] + fake()->randomFloat(2, 0, 2))
                ];
            }
        }

        // Select compatible skin types (1-4 types)
        $compatibleSkinTypes = fake()->randomElements($skinTypes, fake()->numberBetween(1, 4));

        return [
            'base_name' => fake()->unique()->words(3, true) . ' Base Formulation',
            'universal_ingredients' => $selectedIngredients,
            'standard_concentration_ranges' => $concentrationRanges,
            'skin_type_compatibility' => $compatibleSkinTypes,
            'formulation_category' => fake()->randomElement($categories),
            'description' => fake()->sentence(10),
            'created_by' => User::factory(),
            'is_active' => fake()->boolean(90), // 90% active
        ];
    }

    public function active(): static
    {
        return $this->state(['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function forCategory(string $category): static
    {
        return $this->state(['formulation_category' => $category]);
    }

    public function forSkinType(string $skinType): static
    {
        return $this->state(function (array $attributes) use ($skinType) {
            $skinTypes = $attributes['skin_type_compatibility'];
            if (!in_array($skinType, $skinTypes)) {
                $skinTypes[] = $skinType;
            }
            return ['skin_type_compatibility' => $skinTypes];
        });
    }

    public function withIngredients(array $ingredients): static
    {
        return $this->state(function (array $attributes) use ($ingredients) {
            $concentrationRanges = [];
            foreach ($ingredients as $ingredient) {
                if (isset(BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient])) {
                    $concentrationRanges[$ingredient] = BaseFormulation::UNIVERSAL_INGREDIENTS[$ingredient]['concentration_range'];
                }
            }
            
            return [
                'universal_ingredients' => $ingredients,
                'standard_concentration_ranges' => $concentrationRanges,
            ];
        });
    }
}
