<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'System Administrator',
                'email' => 'admin@aletoclan.com',
                'password' => Hash::make('Admin@2024!'),
                'role' => 'admin',
                'phone' => '+2348000000000',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['username' => 'auditor'],
            [
                'name' => 'Community Auditor',
                'email' => 'auditor@aletoclan.org',
                'password' => Hash::make('Audit@2024!'),
                'role' => 'auditor',
                'phone' => '+2348000000001',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['username' => 'govofficial'],
            [
                'name' => 'Government Official',
                'email' => 'gov@aletoclan.org',
                'password' => Hash::make('GovOff@2024!'),
                'role' => 'government_official',
                'phone' => '+2348000000002',
                'email_verified_at' => now(),
            ]
        );
    }
}
