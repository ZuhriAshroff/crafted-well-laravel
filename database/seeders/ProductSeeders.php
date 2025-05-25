<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\BaseFormulation;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create base formulations first
        $formulations = BaseFormulation::factory()->count(10)->create();

        // Create standard products
        Product::factory()
            ->count(30)
            ->customizable()
            ->create();

        // Create premium products
        Product::factory()
            ->count(10)
            ->premium()
            ->customizable()
            ->create();

        // Create specific skin type products
        foreach (['dry', 'oily', 'combination', 'sensitive'] as $skinType) {
            Product::factory()
                ->count(5)
                ->forSkinType($skinType)
                ->create();
        }

        // Create featured products
        Product::factory()
            ->count(5)
            ->state([
                'product_type' => 'limited_edition',
                'standard_price' => fake()->randomFloat(2, 149.99, 399.99),
            ])
            ->create();
    }
}