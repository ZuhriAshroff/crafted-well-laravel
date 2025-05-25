<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_create_profile()
    {
        Sanctum::actingAs($this->user);

        $profileData = [
            'skin_type' => 'oily',
            'primary_skin_concerns' => 'blemish',
            'secondary_skin_concerns' => ['wrinkle'],
            'environmental_factors' => 'urban',
            'allergies' => ['nuts']
        ];

        $response = $this->postJson('/api/profiles', $profileData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Profile created successfully'
                ]);

        $this->assertDatabaseHas('UserProfile', [
            'user_id' => $this->user->user_id,
            'skin_type' => 'oily'
        ]);
    }

    public function test_user_can_get_latest_profile()
    {
        Sanctum::actingAs($this->user);

        $profile = UserProfile::factory()->create(['user_id' => $this->user->user_id]);

        $response = $this->getJson('/api/profiles/latest');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Profile updated successfully'
                ]);

        $this->assertDatabaseHas('UserProfile', [
            'profile_id' => $profile->profile_id,
            'skin_type' => 'dry'
        ]);
    }

    public function test_user_can_delete_profile()
    {
        Sanctum::actingAs($this->user);

        $profile = UserProfile::factory()->create(['user_id' => $this->user->user_id]);

        $response = $this->deleteJson("/api/profiles/{$profile->profile_id}");

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Profile deleted successfully'
                ]);

        $this->assertDatabaseMissing('UserProfile', [
            'profile_id' => $profile->profile_id
        ]);
    }

    public function test_user_cannot_access_other_users_profile()
    {
        $otherUser = User::factory()->create();
        $otherProfile = UserProfile::factory()->create(['user_id' => $otherUser->user_id]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/profiles/{$otherProfile->profile_id}");

        $response->assertStatus(404);
    }

    public function test_get_profile_options()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/profiles/options/all');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'data' => [
                        'skin_types',
                        'skin_concerns',
                        'environmental_factors'
                    ]
                ]);
    }
}