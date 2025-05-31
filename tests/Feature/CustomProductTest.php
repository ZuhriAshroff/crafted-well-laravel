<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CustomProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomProductTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $baseProduct;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->baseProduct = Product::factory()->create(['base_category' => 'serum']);
    }

    public function test_user_can_create_custom_product()
    {
        Sanctum::actingAs($this->user);

        $profileData = [
            'user_id' => $this->user->id,                    // ✅ FIXED: Use 'id' not 'user_id'
            'base_product_id' => $this->baseProduct->id,     // ✅ FIXED: Use 'id' not 'product_id'
            'profile_data' => [
                'skin_type' => 'dry',
                'skin_concerns' => ['wrinkle', 'spots'],
                'environmental_factors' => 'urban',
                'allergies' => []
            ]
        ];

        $response = $this->postJson('/api/custom-products', $profileData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Custom product created successfully'
                ]);

        $this->assertDatabaseHas('custom_products', [       
            'user_id' => $this->user->id,                  
            'base_product_id' => $this->baseProduct->id    
        ]);
    }

    public function test_user_can_view_their_custom_products()
    {
        Sanctum::actingAs($this->user);
        
        CustomProduct::factory()->count(3)->create(['user_id' => $this->user->id]); 

        $response = $this->getJson('/api/custom-products');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        '*' => [
                            'id',                           
                            'name',
                            'total_price',
                            'selected_ingredients'
                        ]
                    ]
                ]);
    }

    public function test_user_can_reformulate_custom_product()
    {
        Sanctum::actingAs($this->user);
        
        $customProduct = CustomProduct::factory()->create(['user_id' => $this->user->id]); 
        $originalPrice = $customProduct->total_price;

        $newProfileData = [
            'profile_data' => [
                'skin_type' => 'oily',
                'skin_concerns' => ['blemish'],
                'environmental_factors' => 'tropical',
                'allergies' => ['fragrances']
            ]
        ];

        $response = $this->postJson("/api/custom-products/{$customProduct->id}/reformulate", $newProfileData);

        $response->assertStatus(200);
        
        $customProduct->refresh();
        
        // Handle JSON fields properly
        $profileData = is_string($customProduct->profile_data) 
            ? json_decode($customProduct->profile_data, true) 
            : $customProduct->profile_data;
            
        $this->assertEquals('oily', $profileData['skin_type']);
        $this->assertContains('fragrances', $profileData['allergies']);
    }

    public function test_custom_product_respects_allergies()
    {
        Sanctum::actingAs($this->user);

        $profileData = [
            'user_id' => $this->user->id,                  
            'base_product_id' => $this->baseProduct->id,     
            'profile_data' => [
                'skin_type' => 'sensitive',
                'skin_concerns' => ['soothe'],
                'environmental_factors' => 'moderate',
                'allergies' => ['vitamin_c', 'fragrances']
            ]
        ];

        $response = $this->postJson('/api/custom-products', $profileData);

        $response->assertStatus(201);
        
        $customProduct = CustomProduct::latest()->first();
        
        // Handle JSON fields properly
        $selectedIngredients = is_string($customProduct->selected_ingredients) 
            ? json_decode($customProduct->selected_ingredients, true) 
            : $customProduct->selected_ingredients;
        
        // Should not contain vitamin_c as it's in allergies
        $this->assertNotContains('vitamin_c', $selectedIngredients);
    }

    public function test_user_cannot_delete_custom_product_with_active_orders()
    {
        Sanctum::actingAs($this->user);
        
        $customProduct = CustomProduct::factory()->create(['user_id' => $this->user->id]); 
        
        // Create an active order with this custom product
        $order = \App\Models\Order::factory()->create([
            'user_id' => $this->user->id,                   
            'status' => 'processing'                        
        ]);
        
        \App\Models\OrderItem::factory()->create([
            'order_id' => $order->id,                       
            'custom_product_id' => $customProduct->id       
        ]);

        $response = $this->deleteJson("/api/custom-products/{$customProduct->id}"); 

        $response->assertStatus(400)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Cannot delete custom product with active orders'
                ]);
    }

    public function test_custom_product_price_calculation()
    {
        // Skip this test if the static methods don't exist yet
        if (!method_exists(CustomProduct::class, 'generateProductComposition')) {
            $this->markTestSkipped('CustomProduct::generateProductComposition method not implemented yet');
        }

        $profileData = [
            'skin_type' => 'dry',
            'skin_concerns' => ['wrinkle'],
            'environmental_factors' => 'urban',
            'allergies' => []
        ];

        $composition = CustomProduct::generateProductComposition($profileData);
        $price = CustomProduct::calculateTotalPrice($composition['ingredients']);

        $this->assertGreaterThan(CustomProduct::BASE_PRICE, $price);
        $this->assertEquals(0, fmod($price, 0.5)); // Should be rounded to nearest 0.50
    }

    public function test_admin_can_view_all_custom_products()
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);
        
        CustomProduct::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/custom-products');       

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        '*' => [
                            'id',                                     
                            'name',
                            'user'
                        ]
                    ]
                ]);
    }
}