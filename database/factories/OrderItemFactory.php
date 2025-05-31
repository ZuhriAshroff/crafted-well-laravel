<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $hasProduct = fake()->boolean(80); // 80% chance of regular product, 20% custom
        
        return [
            'order_id' => Order::factory(),
            'product_id' => $hasProduct ? Product::factory() : null,
            'custom_product_id' => !$hasProduct && class_exists(\App\Models\CustomProduct::class) 
                ? \App\Models\CustomProduct::factory() 
                : null,
            'quantity' => fake()->numberBetween(1, 5),
            'product_price' => fake()->randomFloat(2, 19.99, 199.99),
            'customization_details' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }

    public function forOrder($orderId): static
    {
        return $this->state(['order_id' => $orderId]);
    }

    public function regularProduct(): static
    {
        return $this->state([
            'product_id' => Product::factory(),
            'custom_product_id' => null,
        ]);
    }
}