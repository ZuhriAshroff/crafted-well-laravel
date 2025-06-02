<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin already exists
        $adminExists = User::where('email', 'admin@craftedwell.com')->exists();
        
        if ($adminExists) {
            $this->command->info('Admin user already exists!');
            return;
        }

        // Create admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@craftedwell.com',
            'phone_number' => null,
            'password_hash' => Hash::make('admin123'), // You can change this password
            'password' => Hash::make('admin123'), // For Laravel's default auth
            'registration_date' => now(),
            'last_login' => null,
            'account_status' => 1,
            'email_verified_at' => now(),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'current_team_id' => null,
            'profile_photo_path' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'is_active' => 1,
            'role' => 'admin'
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@craftedwell.com');
        $this->command->info('Password: admin123');
        $this->command->warn('Please change the password after first login!');
    }
}