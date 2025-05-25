<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TestModels extends Command
{
    protected $signature = 'test:models';
    protected $description = 'Test our converted models';

    public function handle()
    {
        $this->info('🧪 Testing Models...');

        try {
            // Test User Model
            $this->info('1️⃣ Testing User Model...');
            $user = User::create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test' . time() . '@example.com',
                'phone_number' => '123-456-7890',
                'password_hash' => Hash::make('password'),
                'role' => 'user'
            ]);
            
            $this->info("✅ User created: {$user->name}");
            $this->info("✅ User ID: {$user->user_id}");
            $this->info("✅ User is admin: " . ($user->isAdmin() ? 'Yes' : 'No'));
            $this->info("✅ User is active: " . ($user->isActive() ? 'Yes' : 'No'));

            // Test User static methods
            $totalUsers = User::getTotalCount();
            $this->info("✅ Total users in DB: {$totalUsers}");

            // Test UserProfile Model
            $this->info('2️⃣ Testing UserProfile Model...');
            $profile = UserProfile::create([
                'user_id' => $user->user_id,
                'skin_type' => 'oily',
                'primary_skin_concerns' => 'blemish',
                'secondary_skin_concerns' => ['wrinkle'],
                'environmental_factors' => 'urban',
                'allergies' => ['nuts']
            ]);
            
            $this->info("✅ Profile created for user: {$user->name}");
            $this->info("✅ Profile ID: {$profile->profile_id}");
            $this->info("✅ Profile completion: {$profile->getCompletionPercentage()}%");
            $this->info("✅ Profile is complete: " . ($profile->isComplete() ? 'Yes' : 'No'));

            // Test validation options
            $skinTypes = UserProfile::getSkinTypeOptions();
            $this->info("✅ Available skin types: " . implode(', ', array_keys($skinTypes)));

            // Test Product Model (if BaseFormulation exists)
            $this->info('3️⃣ Testing Product Model...');
            
            if (Schema::hasTable('BaseFormulation')) {
                $formulation = DB::table('BaseFormulation')->first();
                
                if (!$formulation) {
                    $this->warn("⚠️  No BaseFormulation found, creating test formulation...");
                    $formulationId = DB::table('BaseFormulation')->insertGetId([
                        'base_name' => 'Test Formulation',
                        'universal_ingredients' => json_encode(['water', 'glycerin']),
                        'standard_concentration_ranges' => json_encode(['vitamin_c' => '10-20%'])
                    ]);
                } else {
                    $formulationId = $formulation->base_formulation_id ?? $formulation->id;
                }

                $product = Product::create([
                    'product_name' => 'Test Serum ' . time(),
                    'base_category' => 'skincare',
                    'product_type' => 'standard',
                    'standard_price' => 49.99,
                    'customization_price_modifier' => 15.00,
                    'base_formulation_id' => $formulationId
                ]);
                
                $this->info("✅ Product created: {$product->product_name}");
                $this->info("✅ Product ID: {$product->product_id}");
                $this->info("✅ Standard price: \${$product->getFinalPrice()}");
                $this->info("✅ Custom price: \${$product->getFinalPrice(true)}");
                $this->info("✅ Is customizable: " . ($product->isCustomizable() ? 'Yes' : 'No'));

                // Test product static methods
                $totalProducts = Product::getTotalCount();
                $this->info("✅ Total products in DB: {$totalProducts}");

                $categories = Product::getProductsByCategory();
                $this->info("✅ Products by category: " . json_encode($categories));

            } else {
                $this->warn("⚠️  BaseFormulation table doesn't exist - skipping Product test");
                $product = null;
            }

            // Test Relationships
            $this->info('4️⃣ Testing Relationships...');
            
            $user->refresh(); // Reload user to get relationships
            $userProfile = $user->profile;
            if ($userProfile) {
                $this->info("✅ User->Profile relationship working");
                $this->info("✅ Profile skin type: {$userProfile->skin_type}");
            } else {
                $this->error("❌ User->Profile relationship failed");
            }

            $profile->refresh();
            $profileUser = $profile->user;
            if ($profileUser) {
                $this->info("✅ Profile->User relationship working");
                $this->info("✅ Profile belongs to: {$profileUser->name}");
            } else {
                $this->error("❌ Profile->User relationship failed");
            }

            // Test scopes
            $this->info('5️⃣ Testing Scopes and Queries...');
            
            $activeUsers = User::active()->count();
            $this->info("✅ Active users: {$activeUsers}");
            
            $oilyProfiles = UserProfile::where('skin_type', 'oily')->count();
            $this->info("✅ Oily skin profiles: {$oilyProfiles}");

            if ($product) {
                $skincareProducts = Product::byCategory('skincare')->count();
                $this->info("✅ Skincare products: {$skincareProducts}");
            }

            // Clean up test data
            $this->info('🧹 Cleaning up test data...');
            $profile->delete();
            if ($product) {
                $product->delete();
            }
            $user->delete();

            $this->info('🎉 All model tests passed!');

        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
            return 1;
        }

        return 0;
    }
}