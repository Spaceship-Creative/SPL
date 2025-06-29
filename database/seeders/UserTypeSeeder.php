<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create a Legal Professional test user
        User::firstOrCreate(
            ['email' => 'legal@example.com'],
            [
                'name' => 'Priya Paralegal',
                'email' => 'legal@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'legal_professional',
                'email_verified_at' => now(),
            ]
        );

        // Create a Pro-Se test user
        User::firstOrCreate(
            ['email' => 'prose@example.com'],
            [
                'name' => 'Paul Pro-Se',
                'email' => 'prose@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'pro_se',
                'email_verified_at' => now(),
            ]
        );

        // Create an admin user with legal_professional type
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'user_type' => 'legal_professional',
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->info('Legal Professional: legal@example.com / password');
        $this->command->info('Pro-Se Litigant: prose@example.com / password');
        $this->command->info('Admin: admin@example.com / password');
    }
}
