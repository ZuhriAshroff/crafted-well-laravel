<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class TestFactories extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:factories';

    /**
     * The console command description.
     */
    protected $description = 'Test model factories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏭 Testing Factories...');

        try {
            // Keep track of created records for cleanup
            $createdUsers = collect();
            $createdProfiles = collect();
            $createdProducts = collect();

            // Test User Factory
            $this->info('1️⃣ Testing User Factory...');
            
            // Create regular users
            $users = User::factory()->count(3)->create();
            $createdUsers = $createdUsers->merge($users);
            $this->info("✅ Created {$users->count()} regular users");
            
            // Display sample user
            $sampleUser = $users->first();
            $this->info("   📝 Sample user: {$sampleUser->name} ({$sampleUser->email})");
            $this->info("   📝 Role: {$sampleUser->role}, Active: " . ($sampleUser->isActive() ? 'Yes' : 'No'));

            // Test Admin Factory
            $this->info('2️⃣ Testing Admin User Factory...');
            $admin = User::factory()->admin()->create();
            $createdUsers->push($admin);
            $this->info("✅ Created admin user: {$admin->name}");
            $this->info("   📝 Is admin: " . ($admin->isAdmin() ? 'Yes' : 'No'));

            // Test Inactive User Factory
            $this->info('3️⃣ Testing Inactive User Factory...');
            $inactiveUser = User::factory()->inactive()->create();
            $createdUsers->push($inactiveUser);
            $this->info("✅ Created inactive user: {$inactiveUser->name}");
            $this->info("   📝 Is active: " . ($inactiveUser->isActive() ? 'Yes' : 'No'));

            // Test User with Recent Login
            $this->info('4️⃣ Testing User with Recent Login...');
            $recentUser = User::factory()->recentLogin()->create();
            $createdUsers->push($recentUser);
            $this->info("✅ Created user with recent login: {$recentUser->name}");
            $this->info("   📝 Last login: " . ($recentUser->last_login ? $recentUser->last_login->diffForHumans() : 'Never'));

            // Test UserProfile Factory
            $this->info('5️⃣ Testing UserProfile Factory...');
            $profiles = UserProfile::factory()->count(5)->create();
            $createdProfiles = $createdProfiles->merge($profiles);
            $this->info("✅ Created {$profiles->count()} user profiles");
            
            // Display sample profile
            $sampleProfile = $profiles->first();
            $this->info("   📝 Sample profile: Skin type: {$sampleProfile->skin_type}, Primary concern: {$sampleProfile->primary_skin_concerns}");
            $this->info("   📝 Completion: {$sampleProfile->getCompletionPercentage()}%");

            // Test UserProfile with specific skin types
            $this->info('6️⃣ Testing Skin Type Specific Profiles...');
            $dryProfile = UserProfile::factory()->dry()->create();
            $createdProfiles->push($dryProfile);
            $this->info("✅ Created dry skin profile");

            $sensitiveProfile = UserProfile::factory()->state(['skin_type' => 'sensitive'])->create();
            $createdProfiles->push($sensitiveProfile);
            $this->info("✅ Created sensitive skin profile");

            // Test UserProfile with allergies
            $this->info('7️⃣ Testing Profile with Allergies...');
            $allergyProfile = UserProfile::factory()->withAllergies(['nuts', 'dairy', 'gluten'])->create();
            $createdProfiles->push($allergyProfile);
            $this->info("✅ Created profile with allergies: " . implode(', ', $allergyProfile->allergies));

            // Test Product Factory (if BaseFormulation exists)
            $this->info('8️⃣ Testing Product Factory...');
            
            // Check if we have BaseFormulation data
            $baseFormulationCount = DB::table('BaseFormulation')->count();
            if ($baseFormulationCount == 0) {
                // Create a test base formulation
                $formulationId = DB::table('BaseFormulation')->insertGetId([
                    'base_name' => 'Test Factory Formulation',
                    'universal_ingredients' => json_encode(['water', 'glycerin', 'hyaluronic acid']),
                    'standard_concentration_ranges' => json_encode(['hyaluronic_acid' => '1-2%'])
                ]);
                $this->info("   📝 Created test BaseFormulation for testing");
            }

            // Create regular products
            $products = Product::factory()->count(4)->create();
            $createdProducts = $createdProducts->merge($products);
            $this->info("✅ Created {$products->count()} regular products");
            
            // Display sample product
            $sampleProduct = $products->first();
            $this->info("   📝 Sample product: {$sampleProduct->product_name}");
            $this->info("   📝 Category: {$sampleProduct->base_category}, Price: \${$sampleProduct->standard_price}");

            // Test Premium Products
            $this->info('9️⃣ Testing Premium Product Factory...');
            $premiumProducts = Product::factory()->premium()->count(2)->create();
            $createdProducts = $createdProducts->merge($premiumProducts);
            $this->info("✅ Created {$premiumProducts->count()} premium products");
            
            $samplePremium = $premiumProducts->first();
            $this->info("   📝 Premium product: {$samplePremium->product_name} - \${$samplePremium->standard_price}");

            // Test Customizable Products
            $this->info('🔟 Testing Customizable Product Factory...');
            $customizableProducts = Product::factory()->customizable()->count(2)->create();
            $createdProducts = $createdProducts->merge($customizableProducts);
            $this->info("✅ Created {$customizableProducts->count()} customizable products");
            
            $sampleCustomizable = $customizableProducts->first();
            $this->info("   📝 Customizable product: {$sampleCustomizable->product_name}");
            $this->info("   📝 Base price: \${$sampleCustomizable->standard_price}, Custom modifier: \${$sampleCustomizable->customization_price_modifier}");
            $this->info("   📝 Custom price: \${$sampleCustomizable->getFinalPrice(true)}");

            // Test Products for Specific Skin Types
            $this->info('1️⃣1️⃣ Testing Skin Type Specific Products...');
            $oilyProduct = Product::factory()->forSkinType('oily')->create();
            $createdProducts->push($oilyProduct);
            $this->info("✅ Created product for oily skin: {$oilyProduct->product_name}");

            // Test User with Profile Factory Chain
            $this->info('1️⃣2️⃣ Testing User with Profile Factory Chain...');
            $userWithProfile = User::factory()->withProfile()->create();
            $createdUsers->push($userWithProfile);
            $this->info("✅ Created user with profile: {$userWithProfile->name}");
            $this->info("   📝 Profile exists: " . ($userWithProfile->profile ? 'Yes' : 'No'));

            // Test Data Relationships
            $this->info('1️⃣3️⃣ Testing Factory Data Relationships...');
            
            // Count relationships
            $usersWithProfiles = User::has('profile')->count();
            $profilesWithUsers = UserProfile::has('user')->count();
            
            $this->info("✅ Users with profiles: {$usersWithProfiles}");
            $this->info("✅ Profiles with users: {$profilesWithUsers}");

            // Test Data Quality
            $this->info('1️⃣4️⃣ Testing Data Quality...');
            
            // Check for duplicate emails
            $duplicateEmails = User::select('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->count();
            $this->info("✅ Duplicate emails: {$duplicateEmails} (should be 0)");

            // Check profile completeness
            $completeProfiles = UserProfile::all()->filter(fn($p) => $p->isComplete())->count();
            $totalTestProfiles = $createdProfiles->count();
            $this->info("✅ Complete profiles: {$completeProfiles}/{$totalTestProfiles}");

            // Show Statistics
            $this->info('📊 Factory Test Statistics:');
            $this->table(
                ['Model', 'Created', 'Total in DB'],
                [
                    ['Users', $createdUsers->count(), User::count()],
                    ['UserProfiles', $createdProfiles->count(), UserProfile::count()],
                    ['Products', $createdProducts->count(), Product::count()],
                ]
            );

            // Clean up test data
            $this->info('🧹 Cleaning up test data...');
            
            // Delete in proper order (relationships)
            foreach ($createdProfiles as $profile) {
                $profile->delete();
            }
            $this->info("   ✅ Deleted {$createdProfiles->count()} test profiles");

            foreach ($createdProducts as $product) {
                $product->delete();
            }
            $this->info("   ✅ Deleted {$createdProducts->count()} test products");

            foreach ($createdUsers as $user) {
                // Delete associated profiles first (if any missed)
                $user->profile?->delete();
                $user->delete();
            }
            $this->info("   ✅ Deleted {$createdUsers->count()} test users");

            $this->info('🎉 All factory tests completed successfully!');

        } catch (\Exception $e) {
            $this->error("❌ Factory test failed: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
            
            // Try to clean up on error
            $this->info('🧹 Attempting cleanup after error...');
            try {
                if (isset($createdProfiles)) {
                    foreach ($createdProfiles as $profile) {
                        $profile->delete();
                    }
                }
                if (isset($createdProducts)) {
                    foreach ($createdProducts as $product) {
                        $product->delete();
                    }
                }
                if (isset($createdUsers)) {
                    foreach ($createdUsers as $user) {
                        $user->profile?->delete();
                        $user->delete();
                    }
                }
                $this->info('✅ Cleanup completed');
            } catch (\Exception $cleanup) {
                $this->warn("⚠️ Cleanup failed: " . $cleanup->getMessage());
            }
            
            return 1;
        }

        return 0;
    }
}