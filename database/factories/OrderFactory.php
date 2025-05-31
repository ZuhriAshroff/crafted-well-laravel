<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'total_amount' => fake()->randomFloat(2, 19.99, 499.99),
            'payment_status' => fake()->randomElement(Order::PAYMENT_STATUSES),
            'shipping_status' => fake()->randomElement(Order::SHIPPING_STATUSES),
            'delivery_method' => fake()->randomElement(Order::DELIVERY_METHODS),
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'payment_status' => 'pending',
            'shipping_status' => 'processing',
        ]);
    }

    public function paid(): static
    {
        return $this->state([
            'payment_status' => 'paid',
            'shipping_status' => fake()->randomElement(['processing', 'shipped', 'delivered']),
        ]);
    }

    public function delivered(): static
    {
        return $this->state([
            'payment_status' => 'paid',
            'shipping_status' => 'delivered',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'payment_status' => 'failed',
            'shipping_status' => 'cancelled',
        ]);
    }

    public function withItems(int $itemCount = 3): static
    {
        return $this->afterCreating(function (Order $order) use ($itemCount) {
            \App\Models\OrderItem::factory()
                ->count($itemCount)
                ->create(['order_id' => $order->order_id]);
        });
    }
}
