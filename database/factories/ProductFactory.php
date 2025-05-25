<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_name' => fake()->words(3, true) . ' ' . fake()->randomElement(['Serum', 'Cream', 'Cleanser', 'Moisturizer']),
            'base_category' => fake()->randomElement(Product::VALID_BASE_CATEGORIES),
            'product_type' => fake()->randomElement(Product::VALID_PRODUCT_TYPES),
            'standard_price' => fake()->randomFloat(2, 19.99, 299.99),
            'customization_price_modifier' => fake()->randomFloat(2, 0, 50.00),
            'base_formulation_id' => $this->getOrCreateBaseFormulation(),
        ];
    }

    /**
     * Get existing BaseFormulation or create one
     */
    private function getOrCreateBaseFormulation()
    {
        // Try to get existing BaseFormulation
        $existing = DB::table('BaseFormulation')->first();
        
        if ($existing) {
            return $existing->base_formulation_id ?? $existing->id;
        }

        // Create a new one if none exists
        return DB::table('BaseFormulation')->insertGetId([
            'base_name' => fake()->words(2, true) . ' Base',
            'universal_ingredients' => json_encode([
                'water', 'glycerin', fake()->randomElement(['hyaluronic acid', 'vitamin c', 'retinol', 'niacinamide'])
            ]),
            'standard_concentration_ranges' => json_encode([
                'active_ingredient' => fake()->randomElement(['1-2%', '5-10%', '10-15%', '0.5-1%'])
            ])
        ]);
    }

    public function skincare(): static
    {
        return $this->state(['base_category' => 'skincare']);
    }

    public function premium(): static
    {
        return $this->state([
            'product_type' => 'premium',
            'standard_price' => fake()->randomFloat(2, 99.99, 299.99),
        ]);
    }

    public function customizable(): static
    {
        return $this->state([
            'customization_price_modifier' => fake()->randomFloat(2, 15.00, 75.00),
        ]);
    }

    public function forSkinType(string $skinType): static
    {
        return $this->state(['base_category' => $skinType]);
    }
}