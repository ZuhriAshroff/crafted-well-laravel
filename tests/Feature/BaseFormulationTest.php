<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BaseFormulation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BaseFormulationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
    }

    public function test_admin_can_create_base_formulation()
    {
        Sanctum::actingAs($this->admin);

        $formulationData = [
            'base_name' => 'Test Hydrating Base',
            'universal_ingredients' => ['water', 'glycerin', 'sodium_hyaluronate'],
            'skin_type_compatibility' => ['dry', 'sensitive'],
            'formulation_category' => 'hydrating',
            'description' => 'A hydrating base formulation for dry skin',
        ];

        $response = $this->postJson('/api/base-formulations', $formulationData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Base formulation created successfully'
                ]);

        $this->assertDatabaseHas('BaseFormulation', [
            'base_name' => 'Test Hydrating Base',
            'formulation_category' => 'hydrating'
        ]);
    }

    public function test_regular_user_cannot_create_base_formulation()
    {
        Sanctum::actingAs($this->user);

        $formulationData = [
            'base_name' => 'Test Base',
            'universal_ingredients' => ['water', 'glycerin'],
            'skin_type_compatibility' => ['dry'],
        ];

        $response = $this->postJson('/api/base-formulations', $formulationData);

        $response->assertStatus(403);
    }

    public function test_user_can_view_base_formulations()
    {
        Sanctum::actingAs($this->user);
        
        BaseFormulation::factory()->count(3)->active()->create();

        $response = $this->getJson('/api/base-formulations');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        '*' => [
                            'base_formulation_id',
                            'base_name',
                            'universal_ingredients',
                            'skin_type_compatibility'
                        ]
                    ]
                ]);
    }

    public function test_ingredient_compatibility_validation()
    {
        Sanctum::actingAs($this->user);
        
        $baseFormulation = BaseFormulation::factory()->create([
            'universal_ingredients' => ['water', 'glycerin', 'sodium_hyaluronate']
        ]);

        $response = $this->postJson("/api/base-formulations/{$baseFormulation->base_formulation_id}/validate-compatibility", [
            'ingredients' => ['water', 'glycerin', 'invalid_ingredient']
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'data' => [
                        'is_compatible' => false,
                        'incompatible_ingredients' => ['invalid_ingredient']
                    ]
                ]);
    }

    public function test_concentration_validation()
    {
        Sanctum::actingAs($this->user);
        
        $baseFormulation = BaseFormulation::factory()->create([
            'universal_ingredients' => ['water', 'glycerin'],
            'standard_concentration_ranges' => [
                'water' => ['min' => 60, 'max' => 80],
                'glycerin' => ['min' => 1, 'max' => 10]
            ]
        ]);

        $response = $this->postJson("/api/base-formulations/{$baseFormulation->base_formulation_id}/validate-concentration", [
            'concentrations' => [
                ['ingredient' => 'water', 'concentration' => 70],     // Valid
                ['ingredient' => 'glycerin', 'concentration' => 15]   // Invalid (too high)
            ]
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'data' => [
                        'all_concentrations_valid' => false
                    ]
                ]);
    }

    public function test_skin_type_recommendations()
    {
        Sanctum::actingAs($this->user);
        
        BaseFormulation::factory()->count(2)->active()->forSkinType('dry')->create();
        BaseFormulation::factory()->count(1)->active()->forSkinType('oily')->create();

        $response = $this->postJson('/api/base-formulations/recommendations', [
            'skin_type' => 'dry',
            'limit' => 5
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        '*' => [
                            'base_formulation_id',
                            'base_name',
                            'skin_type_compatibility'
                        ]
                    ],
                    'query_info'
                ]);
    }

    public function test_admin_can_clone_formulation()
    {
        Sanctum::actingAs($this->admin);
        
        $originalFormulation = BaseFormulation::factory()->create([
            'base_name' => 'Original Formulation',
            'universal_ingredients' => ['water', 'glycerin'],
            'skin_type_compatibility' => ['dry', 'sensitive']
        ]);

        $response = $this->postJson("/api/base-formulations/{$originalFormulation->base_formulation_id}/clone", [
            'new_name' => 'Cloned Formulation',
            'description' => 'This is a cloned formulation'
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Base formulation cloned successfully'
                ]);

        $this->assertDatabaseHas('BaseFormulation', [
            'base_name' => 'Cloned Formulation',
            'description' => 'This is a cloned formulation'
        ]);

        // Verify the cloned formulation has the same ingredients and compatibility
        $clonedFormulation = BaseFormulation::where('base_name', 'Cloned Formulation')->first();
        $this->assertEquals($originalFormulation->universal_ingredients, $clonedFormulation->universal_ingredients);
        $this->assertEquals($originalFormulation->skin_type_compatibility, $clonedFormulation->skin_type_compatibility);
    }

    public function test_formulation_creation_validates_concentration_ranges()
    {
        Sanctum::actingAs($this->admin);

        // Try to create formulation with invalid concentration ranges
        $formulationData = [
            'base_name' => 'Invalid Formulation',
            'universal_ingredients' => ['water', 'glycerin'],
            'standard_concentration_ranges' => [
                'water' => ['min' => 80, 'max' => 60], // Invalid: min > max
                'glycerin' => ['min' => 1, 'max' => 10]
            ],
            'skin_type_compatibility' => ['dry']
        ];

        $response = $this->postJson('/api/base-formulations', $formulationData);

        $response->assertStatus(400)
                ->assertJson([
                    'status' => 'error'
                ]);
    }

    public function test_cannot_delete_formulation_in_use()
    {
        Sanctum::actingAs($this->admin);
        
        $baseFormulation = BaseFormulation::factory()->create();
        
        // Create a custom product that uses this formulation
        \App\Models\CustomProduct::factory()->create([
            'base_product_id' => $baseFormulation->base_formulation_id
        ]);

        $response = $this->deleteJson("/api/base-formulations/{$baseFormulation->base_formulation_id}");

        $response->assertStatus(400)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Cannot delete base formulation. It is used in 1 custom products.'
                ]);
    }

    public function test_formulation_deactivation()
    {
        Sanctum::actingAs($this->admin);
        
        $baseFormulation = BaseFormulation::factory()->active()->create();

        $response = $this->postJson("/api/base-formulations/{$baseFormulation->base_formulation_id}/deactivate");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Base formulation deactivated successfully'
                ]);

        $this->assertDatabaseHas('BaseFormulation', [
            'base_formulation_id' => $baseFormulation->base_formulation_id,
            'is_active' => false
        ]);
    }

    public function test_compatible_formulations_endpoint()
    {
        Sanctum::actingAs($this->user);
        
        BaseFormulation::factory()->active()->forSkinType('dry')->forCategory('hydrating')->create();
        BaseFormulation::factory()->active()->forSkinType('oily')->forCategory('acne_treatment')->create();
        BaseFormulation::factory()->active()->forSkinType('dry')->forCategory('anti_aging')->create();

        $response = $this->postJson('/api/base-formulations/compatible', [
            'skin_types' => ['dry', 'oily'],
            'category' => 'hydrating'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'dry' => ['*'],
                        'oily' => ['*']
                    ],
                    'summary'
                ]);
    }

    public function test_formulation_analytics_admin_only()
    {
        Sanctum::actingAs($this->admin);
        
        BaseFormulation::factory()->count(5)->create();

        $response = $this->getJson('/api/base-formulations/admin/analytics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'total_formulations',
                        'active_formulations',
                        'formulations_by_category',
                        'skin_type_coverage',
                        'most_used_ingredients',
                        'recent_activity',
                        'usage_statistics'
                    ]
                ]);
    }

    public function test_regular_user_cannot_access_analytics()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/base-formulations/admin/analytics');

        $response->assertStatus(403);
    }
}