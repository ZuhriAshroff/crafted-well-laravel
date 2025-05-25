<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class TestApi extends Command
{
    protected $signature = 'test:api';
    protected $description = 'Test API endpoints';

    public function handle()
    {
        $this->info('🌐 Testing API Endpoints...');

        try {
            // Create test user and get token
            $user = User::factory()->create();
            $token = $user->createToken('test-token')->plainTextToken;
            
            $this->info("👤 Created test user: {$user->email}");

            // Test base URL
            $baseUrl = 'http://localhost:8000'; // Adjust if needed
            
            // Test UserProfile API
            $this->info('1️⃣ Testing UserProfile API...');
            
            // GET profiles (empty initially)
            $response = Http::withToken($token)->get("{$baseUrl}/api/profiles");
            $this->info("✅ GET /api/profiles - Status: {$response->status()}");
            
            // POST create profile
            $profileData = [
                'skin_type' => 'oily',
                'primary_skin_concerns' => 'blemish',
                'secondary_skin_concerns' => ['wrinkle'],
                'environmental_factors' => 'urban',
                'allergies' => ['nuts']
            ];

            $response = Http::withToken($token)->post("{$baseUrl}/api/profiles", $profileData);
            $this->info("✅ POST /api/profiles - Status: {$response->status()}");
            
            if ($response->successful()) {
                $profileId = $response->json('data.profile_id');
                $this->info("✅ Profile created with ID: {$profileId}");
                
                // GET specific profile
                $response = Http::withToken($token)->get("{$baseUrl}/api/profiles/{$profileId}");
                $this->info("✅ GET /api/profiles/{$profileId} - Status: {$response->status()}");
            }

            // Test Products API
            $this->info('2️⃣ Testing Products API...');
            
            // GET products
            $response = Http::withToken($token)->get("{$baseUrl}/api/products");
            $this->info("✅ GET /api/products - Status: {$response->status()}");
            
            // GET product options
            $response = Http::withToken($token)->get("{$baseUrl}/api/products/options");
            $this->info("✅ GET /api/products/options - Status: {$response->status()}");

            // Test Admin API (should fail with regular user)
            $this->info('3️⃣ Testing Admin Protection...');
            
            $productData = [
                'product_name' => 'Test Product',
                'base_category' => 'skincare',
                'product_type' => 'standard',
                'standard_price' => 49.99,
                'customization_price_modifier' => 15.00,
                'base_formulation_id' => 1
            ];

            $response = Http::withToken($token)->post("{$baseUrl}/api/products", $productData);
            if ($response->status() === 403) {
                $this->info("✅ Admin protection working - Status: {$response->status()}");
            } else {
                $this->warn("⚠️  Expected 403 for non-admin, got: {$response->status()}");
            }

            // Test with admin user
            $admin = User::factory()->admin()->create();
            $adminToken = $admin->createToken('admin-token')->plainTextToken;
            
            $response = Http::withToken($adminToken)->post("{$baseUrl}/api/products", $productData);
            $this->info("✅ Admin POST /api/products - Status: {$response->status()}");

            // Clean up
            $user->tokens()->delete();
            $admin->tokens()->delete();
            $user->delete();
            $admin->delete();

            $this->info('🎉 API tests completed!');

        } catch (\Exception $e) {
            $this->error("❌ API test failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}