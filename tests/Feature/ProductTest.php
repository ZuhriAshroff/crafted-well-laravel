<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\BaseFormulation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
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

    public function test_user_can_view_products()
    {
        Sanctum::actingAs($this->user);
        
        $products = Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        '*' => [
                            'product_id',
                            'product_name',
                            'base_category',
                            'standard_price'
                        ]
                    ],
                    'pagination'
                ]);
    }

    public function test_admin_can_create_product()
    {
        Sanctum::actingAs($this->admin);
        
        $formulation = BaseFormulation::factory()->create();

        $productData = [
            'product_name' => 'Test Serum',
            'base_category' => 'skincare',
            'product_type' => 'standard',
            'standard_price' => 49.99,
            'customization_price_modifier' => 15.00,
            'base_formulation_id' => $formulation->base_formulation_id,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Product created successfully'
                ]);

        $this->assertDatabaseHas('Product', [
            'product_name' => 'Test Serum',
            'base_category' => 'skincare'
        ]);
    }

    public function test_regular_user_cannot_create_product()
    {
        Sanctum::actingAs($this->user);
        
        $productData = [
            'product_name' => 'Test Serum',
            'base_category' => 'skincare',
            'product_type' => 'standard',
            'standard_price' => 49.99,
            'customization_price_modifier' => 15.00,
            'base_formulation_id' => 1,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(403);
    }

    public function test_user_can_search_products()
    {
        Sanctum::actingAs($this->user);
        
        Product::factory()->create(['product_name' => 'Vitamin C Serum']);
        Product::factory()->create(['product_name' => 'Retinol Cream']);

        $response = $this->getJson('/api/products/search?q=Vitamin');

        $response->assertStatus(200)
                ->assertJsonPath('data.0.product_name', 'Vitamin C Serum');
    }

    public function test_user_can_get_product_recommendations()
    {
        Sanctum::actingAs($this->user);
        
        // Create user profile
        $profile = \App\Models\UserProfile::factory()->create([
            'user_id' => $this->user->user_id,
            'skin_type' => 'oily'
        ]);

        // Create products for oily skin
        Product::factory()->count(3)->forSkinType('oily')->create();

        $response = $this->getJson('/api/products/recommendations');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data',
                    'count',
                    'based_on_profile'
                ]);
    }

    public function test_product_validation_rules()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/products', [
            'product_name' => '', // Required field missing
            'standard_price' => -10, // Invalid price
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['product_name', 'standard_price']);
    }
}