<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_user_can_create_order()
    {
        Sanctum::actingAs($this->user);
        
        $product = Product::factory()->create();

        $orderData = [
            'total_amount' => 99.99,
            'delivery_method' => 'standard',
            'items' => [
                [
                    'product_id' => $product->product_id,
                    'quantity' => 2,
                    'product_price' => 49.99,
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Order created successfully'
                ]);

        $this->assertDatabaseHas('Order', [
            'user_id' => $this->user->user_id,
            'total_amount' => 99.99
        ]);
    }

    public function test_user_can_view_their_orders()
    {
        Sanctum::actingAs($this->user);
        
        Order::factory()->count(3)->create(['user_id' => $this->user->user_id]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        '*' => [
                            'order_id',
                            'total_amount',
                            'payment_status',
                            'items'
                        ]
                    ]
                ]);
    }

    public function test_user_can_cancel_order()
    {
        Sanctum::actingAs($this->user);
        
        $order = Order::factory()->pending()->create(['user_id' => $this->user->user_id]);

        $response = $this->postJson("/api/orders/{$order->order_id}/cancel");

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('Order', [
            'order_id' => $order->order_id,
            'shipping_status' => 'cancelled'
        ]);
    }

    public function test_admin_can_update_order_status()
    {
        Sanctum::actingAs($this->admin);
        
        $order = Order::factory()->create();

        $response = $this->putJson("/api/orders/admin/orders/{$order->order_id}", [
            'payment_status' => 'paid',
            'shipping_status' => 'shipped'
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('Order', [
            'order_id' => $order->order_id,
            'payment_status' => 'paid',
            'shipping_status' => 'shipped'
        ]);
    }

    public function test_user_cannot_cancel_shipped_order()
    {
        Sanctum::actingAs($this->user);
        
        $order = Order::factory()->state([
            'user_id' => $this->user->user_id,
            'payment_status' => 'paid',
            'shipping_status' => 'shipped'
        ])->create();

        $response = $this->postJson("/api/orders/{$order->order_id}/cancel");

        $response->assertStatus(400)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Order cannot be cancelled in its current status'
                ]);
    }
}