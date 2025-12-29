<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VerificationDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@neuromon.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        // Pre-approved Doctor
        User::updateOrCreate(
            ['email' => 'doctor.approved@neuromon.local'],
            [
                'name' => 'Dr. Approved',
                'password' => Hash::make('Doctor123!'),
                'role' => 'doctor',
                'status' => 'active',
                'specialty' => 'Neurology',
            ]
        );
    }
}
